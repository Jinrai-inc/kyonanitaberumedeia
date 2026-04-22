(function($) {
    'use strict';

    var SCENES = (typeof kntGenerator !== 'undefined' && kntGenerator.scenes) ? kntGenerator.scenes : {};

    // ページロード時に前回の結果表示を非表示
    $('#knt-generator-result').hide();
    $('#knt-generator-error').hide();
    $('#knt-generator-loading').hide();

    // ジャンル選択時にジャンル名を自動更新
    $('#knt-genre').on('change', function() {
        var selected = $(this).find(':selected');
        $('#knt-genre-keyword').val(selected.data('name') || '');
    });

    // モード切替（ジャンル ↔ シーン）
    $('#knt-mode').on('change', function() {
        var mode = $(this).val();
        if (mode === 'scene') {
            $('#knt-genre-row').hide();
            $('#knt-scene-row').show();
        } else {
            $('#knt-genre-row').show();
            $('#knt-scene-row').hide();
            $('#knt-scene-info-row').hide();
        }
    });

    // シーン選択時に情報プレビュー
    $('#knt-scene').on('change', function() {
        var key = $(this).val();
        if (!key || !SCENES[key]) {
            $('#knt-scene-info-row').hide();
            $('#knt-scene-description').text('');
            return;
        }
        var scene = SCENES[key];
        $('#knt-scene-description').text(scene.description);
        $('#knt-allowed-genres').text(scene.allowed_genres_labels.join(', '));
        $('#knt-excluded-genres').text(
            scene.excluded_genres_labels.length > 0
                ? scene.excluded_genres_labels.join(', ')
                : 'なし'
        );

        var filters = [];
        if (scene.api_filters.private_room) filters.push('個室あり');
        if (scene.api_filters.free_drink)   filters.push('飲み放題');
        if (scene.api_filters.free_food)    filters.push('食べ放題');
        if (scene.api_filters.lunch)        filters.push('ランチあり');
        if (scene.api_filters.midnight)     filters.push('深夜営業');
        if (scene.api_filters.child)        filters.push('お子様連れOK');
        if (scene.api_filters.course)       filters.push('コースあり');
        $('#knt-api-filters').text(filters.length > 0 ? filters.join(', ') : 'なし');

        $('#knt-scene-info-row').show();
    });

    // フォーム送信
    $('#knt-generator-form').on('submit', function(e) {
        e.preventDefault();

        var $btn = $('#knt-generate-btn');
        var $loading = $('#knt-generator-loading');
        var $result = $('#knt-generator-result');
        var $error = $('#knt-generator-error');

        $btn.prop('disabled', true).text('生成中...');
        $loading.show();
        $result.hide();
        $error.hide();

        var selectedStation = $('#knt-station').find(':selected');
        var data = {
            action: 'knt_generate_article',
            nonce: kntGenerator.nonce,
            area: $('#knt-area').val(),
            mode: $('#knt-mode').val(),
            count: $('#knt-count').val(),
            category_1: $('#knt-category-1').val(),
            category_2: $('#knt-category-2').val(),
            station_name: selectedStation.val() || '',
            station_lat: selectedStation.data('lat') || '',
            station_lng: selectedStation.data('lng') || '',
            allow_update: $('#knt-allow-update').is(':checked') ? 1 : 0,
        };

        if (data.mode === 'scene') {
            data.scene = $('#knt-scene').val();
        } else {
            data.genre_name = $('#knt-genre-keyword').val();
            data.genre_code = $('#knt-genre').val();
        }

        $.ajax({
            url: kntGenerator.ajaxUrl,
            type: 'POST',
            data: data,
            success: function(response) {
                $loading.hide();
                $btn.prop('disabled', false).text('記事を生成する');
                if (response.success) {
                    $result.show();
                    $('#knt-edit-link').attr('href', response.data.edit_url);
                    $('#knt-preview-link').attr('href', response.data.preview_url);
                } else {
                    $error.show();
                    $('#knt-error-message').text(response.data);
                }
            },
            error: function() {
                $loading.hide();
                $btn.prop('disabled', false).text('記事を生成する');
                $error.show();
                $('#knt-error-message').text('通信エラーが発生しました。');
            }
        });
    });

    // ========================================
    // 駅プルダウン連動（市区町村選択時）
    // ========================================
    var STATION_DATA = (typeof kntGenerator !== 'undefined' && kntGenerator.stationData) ? kntGenerator.stationData : {};

    $('#knt-area-city').on('change', function() {
        var cityId = $(this).val();
        var $station = $('#knt-station');
        $station.html('<option value="">駅を選択しない（市区町村全体で検索）</option>');
        $('#knt-station-lat').val('');
        $('#knt-station-lng').val('');

        if (cityId && STATION_DATA[cityId]) {
            STATION_DATA[cityId].forEach(function(st) {
                $station.append(
                    '<option value="' + st.name + '" data-lat="' + st.lat + '" data-lng="' + st.lng + '">'
                    + st.name + '（' + st.lines + '）</option>'
                );
            });
        }
    });

    $('#knt-station').on('change', function() {
        var sel = $(this).find(':selected');
        $('#knt-station-lat').val(sel.data('lat') || '');
        $('#knt-station-lng').val(sel.data('lng') || '');
        // 駅選択時はエリア名を駅名に更新
        if (sel.val()) {
            $('#knt-area').val(sel.val().replace('駅', '') || '');
        }
    });

    // ========================================
    // 一括生成
    // ========================================
    $('#knt-bulk-btn').on('click', function() {
        var prefId = $('#knt-area-pref').val();
        if (!prefId || !kntAreaCities || !kntAreaCities[prefId]) {
            alert('都道府県を選択してください。');
            return;
        }

        var cities = kntAreaCities[prefId];
        if (cities.length === 0) {
            alert('この都道府県には市区町村カテゴリが登録されていません。');
            return;
        }

        var mode = $('#knt-mode').val();
        var genreName = $('#knt-genre-keyword').val() || 'グルメ';
        var genreCode = $('#knt-genre').val() || '';
        var sceneKey = $('#knt-scene').val() || '';
        var count = $('#knt-count').val() || 5;
        var modeLabel = mode === 'scene' ? ('シーン: ' + $('#knt-scene option:selected').text()) : ('ジャンル: ' + genreName);

        if (!confirm(cities.length + '件の市区町村で記事を一括生成します。\n\n' + modeLabel + '\n\nこの処理にはしばらく時間がかかります。よろしいですか？')) {
            return;
        }

        var $btn = $(this);
        var $loading = $('#knt-generator-loading');
        var $result = $('#knt-generator-result');
        var $error = $('#knt-generator-error');

        $btn.prop('disabled', true);
        $('#knt-generate-btn').prop('disabled', true);
        $result.hide();
        $error.hide();

        // 結果表示エリアを作成
        var $bulkLog = $('<div id="knt-bulk-log" style="margin-top:20px;"></div>');
        $('#knt-generator-form').after($bulkLog);
        $bulkLog.html('<h3>一括生成中... (0/' + cities.length + ')</h3><div id="knt-bulk-items"></div>');

        var completed = 0;
        var errors = 0;
        var queue = cities.slice();

        function processNext() {
            if (queue.length === 0) {
                $bulkLog.find('h3').text('一括生成完了！ (' + (completed - errors) + '件成功 / ' + errors + '件エラー)');
                $btn.prop('disabled', false);
                $('#knt-generate-btn').prop('disabled', false);
                return;
            }

            var city = queue.shift();
            completed++;
            $bulkLog.find('h3').text('一括生成中... (' + completed + '/' + cities.length + ') - ' + city.name);

            $.ajax({
                url: kntGenerator.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'knt_generate_bulk',
                    nonce: kntGenerator.nonce,
                    area: city.name,
                    city_id: city.id,
                    pref_id: prefId,
                    mode: mode,
                    genre_name: genreName,
                    genre_code: genreCode,
                    scene: sceneKey,
                    count: count,
                    allow_update: $('#knt-allow-update').is(':checked') ? 1 : 0
                },
                success: function(response) {
                    if (response.success) {
                        $('#knt-bulk-items').prepend('<p style="color:green;">✅ ' + response.data.area + ' → ' + response.data.title + '</p>');
                    } else {
                        errors++;
                        $('#knt-bulk-items').prepend('<p style="color:red;">❌ ' + city.name + ': ' + response.data + '</p>');
                    }
                    // API負荷軽減のため2秒待つ
                    setTimeout(processNext, 2000);
                },
                error: function() {
                    errors++;
                    $('#knt-bulk-items').prepend('<p style="color:red;">❌ ' + city.name + ': 通信エラー</p>');
                    setTimeout(processNext, 2000);
                }
            });
        }

        processNext();
    });

    // ========================================
    // 駅一括生成
    // ========================================
    $('#knt-bulk-station-btn').on('click', function() {
        var cityId = $('#knt-area-city').val();
        var prefId = $('#knt-area-pref').val();
        if (!cityId) {
            alert('市区町村を選択してください。');
            return;
        }
        if (!STATION_DATA[cityId] || STATION_DATA[cityId].length === 0) {
            alert('この市区町村には駅データが登録されていません。');
            return;
        }

        var stations = STATION_DATA[cityId];
        var mode = $('#knt-mode').val();
        var genreName = $('#knt-genre-keyword').val() || 'グルメ';
        var genreCode = $('#knt-genre').val() || '';
        var sceneKey = $('#knt-scene').val() || '';
        var count = $('#knt-count').val() || 5;
        var cityName = $('#knt-area-city').find(':selected').data('name') || '';
        var modeLabel = mode === 'scene' ? ('シーン: ' + $('#knt-scene option:selected').text()) : ('ジャンル: ' + genreName);

        if (!confirm(stations.length + '駅で記事を一括生成します。\n\n' + cityName + ' の駅: ' + stations.map(function(s){ return s.name; }).join('、') + '\n' + modeLabel + '\n\nよろしいですか？')) {
            return;
        }

        var $btn = $(this);
        $btn.prop('disabled', true);
        $('#knt-generate-btn').prop('disabled', true);
        $('#knt-bulk-btn').prop('disabled', true);
        $('#knt-generator-result').hide();
        $('#knt-generator-error').hide();

        var $bulkLog = $('#knt-bulk-log');
        if (!$bulkLog.length) {
            $bulkLog = $('<div id="knt-bulk-log" style="margin-top:20px;"></div>');
            $('#knt-generator-form').after($bulkLog);
        }
        $bulkLog.html('<h3>駅別一括生成中... (0/' + stations.length + ')</h3><div id="knt-bulk-items"></div>');

        var completed = 0;
        var errors = 0;
        var queue = stations.slice();

        function processNextStation() {
            if (queue.length === 0) {
                $bulkLog.find('h3').text('駅別一括生成完了！ (' + (completed - errors) + '件成功 / ' + errors + '件エラー)');
                $btn.prop('disabled', false);
                $('#knt-generate-btn').prop('disabled', false);
                $('#knt-bulk-btn').prop('disabled', false);
                return;
            }

            var st = queue.shift();
            completed++;
            $bulkLog.find('h3').text('駅別一括生成中... (' + completed + '/' + stations.length + ') - ' + st.name);

            $.ajax({
                url: kntGenerator.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'knt_generate_article',
                    nonce: kntGenerator.nonce,
                    area: cityName,
                    mode: mode,
                    count: count,
                    genre_name: genreName,
                    genre_code: genreCode,
                    scene: sceneKey,
                    station_name: st.name,
                    station_lat: st.lat,
                    station_lng: st.lng,
                    category_1: prefId,
                    category_2: cityId,
                    allow_update: $('#knt-allow-update').is(':checked') ? 1 : 0
                },
                success: function(response) {
                    if (response.success) {
                        $('#knt-bulk-items').prepend('<p style="color:green;">✅ ' + st.name + ' → <a href="' + response.data.edit_url + '" target="_blank">' + (response.data.title || 'ID:' + response.data.post_id) + '</a></p>');
                    } else {
                        errors++;
                        $('#knt-bulk-items').prepend('<p style="color:red;">❌ ' + st.name + ': ' + response.data + '</p>');
                    }
                    setTimeout(processNextStation, 2000);
                },
                error: function() {
                    errors++;
                    $('#knt-bulk-items').prepend('<p style="color:red;">❌ ' + st.name + ': 通信エラー</p>');
                    setTimeout(processNextStation, 2000);
                }
            });
        }

        processNextStation();
    });

    // ========================================
    // 地方別一括生成
    // ========================================
    var REGION_LABELS = {
        'hokkaido_tohoku': '北海道・東北',
        'kanto': '関東（東京以外）',
        'tokyo23': '東京23区',
        'tokyo_tama': '東京多摩',
        'chubu': '中部・北陸',
        'kansai': '関西',
        'chugoku_shikoku': '中国・四国',
        'kyushu': '九州・沖縄'
    };

    $('.knt-region-bulk-btn').on('click', function() {
        var region = $(this).data('region');
        var regionGroups = (typeof kntGenerator !== 'undefined' && kntGenerator.regionGroups) ? kntGenerator.regionGroups : {};
        var stations = regionGroups[region];

        if (!stations || stations.length === 0) {
            alert('この地方の駅データがありません。');
            return;
        }

        var mode = $('#knt-mode').val();
        var genreName = $('#knt-genre-keyword').val() || 'グルメ';
        var genreCode = $('#knt-genre').val() || '';
        var sceneKey = $('#knt-scene').val() || '';
        var count = $('#knt-count').val() || 10;
        var regionLabel = REGION_LABELS[region] || region;
        var modeLabel = mode === 'scene' ? ('シーン: ' + $('#knt-scene option:selected').text()) : ('ジャンル: ' + genreName);

        if (!confirm('[' + regionLabel + '] ' + stations.length + '駅で記事を一括生成します。\n\n' + modeLabel + '\n所要時間: 約' + Math.ceil(stations.length * 2 / 60) + '分\n\nよろしいですか？')) {
            return;
        }

        // 全ボタンを無効化
        $('.knt-region-bulk-btn').prop('disabled', true);
        $('#knt-generate-btn').prop('disabled', true);
        $('#knt-bulk-btn').prop('disabled', true);
        $('#knt-bulk-station-btn').prop('disabled', true);
        $('#knt-generator-result').hide();
        $('#knt-generator-error').hide();

        var $bulkLog = $('#knt-bulk-log');
        if (!$bulkLog.length) {
            $bulkLog = $('<div id="knt-bulk-log" style="margin-top:20px;"></div>');
            $('#knt-generator-form').after($bulkLog);
        }
        $bulkLog.html('<h3>[' + regionLabel + '] 一括生成中... (0/' + stations.length + ')</h3><div id="knt-bulk-items"></div>');

        var completed = 0;
        var errors = 0;
        var successes = 0;
        var queue = stations.slice();

        function processNextRegion() {
            if (queue.length === 0) {
                $bulkLog.find('h3').text('[' + regionLabel + '] 完了！ (' + successes + '件成功 / ' + errors + '件エラー)');
                $('.knt-region-bulk-btn').prop('disabled', false);
                $('#knt-generate-btn').prop('disabled', false);
                $('#knt-bulk-btn').prop('disabled', false);
                $('#knt-bulk-station-btn').prop('disabled', false);
                return;
            }

            var st = queue.shift();
            completed++;
            $bulkLog.find('h3').text('[' + regionLabel + '] 一括生成中... (' + completed + '/' + stations.length + ') - ' + st.name);

            $.ajax({
                url: kntGenerator.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'knt_generate_article',
                    nonce: kntGenerator.nonce,
                    area: st.city,
                    mode: mode,
                    count: count,
                    genre_name: genreName,
                    genre_code: genreCode,
                    scene: sceneKey,
                    station_name: st.name,
                    station_lat: st.lat,
                    station_lng: st.lng,
                    category_1: st.pref_id,
                    category_2: st.city_id,
                    allow_update: $('#knt-allow-update').is(':checked') ? 1 : 0
                },
                success: function(response) {
                    if (response.success) {
                        successes++;
                        $('#knt-bulk-items').prepend('<p style="color:green;">&#x2705; ' + st.name + '（' + st.city + '）→ <a href="' + response.data.edit_url + '" target="_blank">' + (response.data.title || '') + '</a></p>');
                    } else {
                        errors++;
                        $('#knt-bulk-items').prepend('<p style="color:red;">&#x274C; ' + st.name + '（' + st.city + '）: ' + response.data + '</p>');
                    }
                    setTimeout(processNextRegion, 2000);
                },
                error: function() {
                    errors++;
                    $('#knt-bulk-items').prepend('<p style="color:red;">&#x274C; ' + st.name + ': 通信エラー</p>');
                    setTimeout(processNextRegion, 2000);
                }
            });
        }

        processNextRegion();
    });
})(jQuery);
