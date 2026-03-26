(function($) {
    'use strict';

    $('#knt-genre').on('change', function() {
        var selected = $(this).find(':selected');
        var name = selected.data('name') || '';
        $('#knt-genre-keyword').val(name);
    });

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

        $.ajax({
            url: kntGenerator.ajaxUrl,
            type: 'POST',
            data: {
                action: 'knt_generate_article',
                nonce: kntGenerator.nonce,
                area: $('#knt-area').val(),
                genre_name: $('#knt-genre-keyword').val(),
                genre_code: $('#knt-genre').val(),
                count: $('#knt-count').val(),
                category_1: $('#knt-category-1').val(),
                category_2: $('#knt-category-2').val(),
            },
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
