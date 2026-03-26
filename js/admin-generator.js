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
})(jQuery);
