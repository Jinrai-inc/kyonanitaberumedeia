(function($) {
    'use strict';

    var SCENES = (typeof kntGenerator !== 'undefined' && kntGenerator.scenes) ? kntGenerator.scenes : {};

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

        var data = {
            action: 'knt_generate_article',
            nonce: kntGenerator.nonce,
            area: $('#knt-area').val(),
            mode: $('#knt-mode').val(),
            count: $('#knt-count').val(),
            category_1: $('#knt-category-1').val(),
            category_2: $('#knt-category-2').val(),
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
                    count: count
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
})(jQuery);
