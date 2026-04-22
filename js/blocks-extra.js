(function(wp) {
    'use strict';
    var el = wp.element.createElement;
    var registerBlockType = wp.blocks.registerBlockType;
    var RichText = wp.blockEditor.RichText;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var MediaUpload = wp.blockEditor.MediaUpload;
    var PanelBody = wp.components.PanelBody;
    var TextControl = wp.components.TextControl;
    var SelectControl = wp.components.SelectControl;
    var ToggleControl = wp.components.ToggleControl;
    var Button = wp.components.Button;

    var CATEGORY = 'knt-media';

    // ========================================
    // 1. メニューリストブロック (knt/menu-list)
    // ========================================
    registerBlockType('knt/menu-list', {
    apiVersion: 3,
        title: 'メニューリスト',
        icon: 'clipboard',
        category: CATEGORY,
        attributes: {
            items: { type: 'array', default: [{ dish: '', price: '', desc: '', recommended: false }] }
        },
        edit: function(props) {
            var items = props.attributes.items;
            function updateItem(index, key, value) {
                var updated = items.slice();
                updated[index] = Object.assign({}, updated[index]);
                updated[index][key] = value;
                props.setAttributes({ items: updated });
            }
            function addItem() {
                props.setAttributes({ items: items.concat([{ dish: '', price: '', desc: '', recommended: false }]) });
            }
            function removeItem(index) {
                props.setAttributes({ items: items.filter(function(_, i) { return i !== index; }) });
            }
            return el('div', { className: 'knt-menu-list-editor' },
                items.map(function(item, i) {
                    return el('div', { key: i, style: { display: 'flex', gap: '8px', marginBottom: '8px', alignItems: 'center' } },
                        el('input', { value: item.dish, placeholder: '料理名', style: { flex: 2 },
                            onChange: function(e) { updateItem(i, 'dish', e.target.value); } }),
                        el('input', { value: item.price, placeholder: '¥1,200', style: { flex: 1 },
                            onChange: function(e) { updateItem(i, 'price', e.target.value); } }),
                        el('input', { value: item.desc || '', placeholder: '説明（任意）', style: { flex: 2 },
                            onChange: function(e) { updateItem(i, 'desc', e.target.value); } }),
                        el('button', { onClick: function() { removeItem(i); }, style: { color: 'red' } }, 'x')
                    );
                }),
                el('button', { className: 'components-button is-secondary', onClick: addItem }, '+ メニューを追加')
            );
        },
        save: function(props) {
            return el('div', { className: 'knt-menu-list' },
                props.attributes.items.map(function(item, i) {
                    if (!item.dish) return null;
                    return el('div', { key: i, className: 'knt-menu-list__item' + (item.recommended ? ' is-recommended' : '') },
                        el('span', { className: 'knt-menu-list__dish' }, item.dish),
                        item.desc ? el('span', { className: 'knt-menu-list__desc' }, item.desc) : null,
                        el('span', { className: 'knt-menu-list__price' }, item.price)
                    );
                })
            );
        }
    });

    // ========================================
    // 2. 吹き出しブロック (knt/balloon)
    // ========================================
    registerBlockType('knt/balloon', {
    apiVersion: 3,
        title: '吹き出しブロック',
        icon: 'format-chat',
        category: CATEGORY,
        attributes: {
            imageUrl: { type: 'string', default: '' },
            imageId: { type: 'number', default: 0 },
            name: { type: 'string', default: '' },
            text: { type: 'string', default: '' },
            position: { type: 'string', default: 'left' },
            type: { type: 'string', default: 'normal' }
        },
        edit: function(props) {
            var a = props.attributes;
            return el('div', {},
                el(InspectorControls, {},
                    el(PanelBody, { title: '吹き出し設定' },
                        el(SelectControl, { label: '位置', value: a.position, options: [
                            { label: '左', value: 'left' }, { label: '右', value: 'right' }
                        ], onChange: function(v) { props.setAttributes({ position: v }); } }),
                        el(SelectControl, { label: 'タイプ', value: a.type, options: [
                            { label: '通常', value: 'normal' }, { label: '考え中', value: 'thinking' }
                        ], onChange: function(v) { props.setAttributes({ type: v }); } }),
                        el(TextControl, { label: '名前', value: a.name,
                            onChange: function(v) { props.setAttributes({ name: v }); } }),
                        el(MediaUpload, {
                            onSelect: function(media) { props.setAttributes({ imageUrl: media.url, imageId: media.id }); },
                            allowedTypes: ['image'],
                            render: function(obj) {
                                return el(Button, { onClick: obj.open, className: 'components-button is-secondary' },
                                    a.imageUrl ? '画像を変更' : 'アバター画像を選択');
                            }
                        })
                    )
                ),
                el('div', { className: 'knt-balloon knt-balloon--' + a.position + ' knt-balloon--' + a.type },
                    el('div', { className: 'knt-balloon__avatar' },
                        a.imageUrl ? el('img', { src: a.imageUrl, alt: a.name }) : el('span', {}, '?'),
                        a.name ? el('span', { className: 'knt-balloon__name' }, a.name) : null
                    ),
                    el('div', { className: 'knt-balloon__body' },
                        el(RichText, { tagName: 'p', value: a.text, placeholder: 'テキストを入力...',
                            onChange: function(v) { props.setAttributes({ text: v }); } })
                    )
                )
            );
        },
        save: function(props) {
            var a = props.attributes;
            return el('div', { className: 'knt-balloon knt-balloon--' + a.position + ' knt-balloon--' + a.type },
                el('div', { className: 'knt-balloon__avatar' },
                    a.imageUrl ? el('img', { src: a.imageUrl, alt: a.name }) : null,
                    a.name ? el('span', { className: 'knt-balloon__name' }, a.name) : null
                ),
                el('div', { className: 'knt-balloon__body' },
                    el(RichText.Content, { tagName: 'p', value: a.text })
                )
            );
        }
    });

    // ========================================
    // 3. ボックスブロック (knt/box)
    // ========================================
    registerBlockType('knt/box', {
    apiVersion: 3,
        title: 'ボックスブロック',
        icon: 'editor-table',
        category: CATEGORY,
        attributes: {
            type: { type: 'string', default: 'point' },
            title: { type: 'string', default: '' },
            content: { type: 'string', default: '' }
        },
        edit: function(props) {
            var a = props.attributes;
            var types = [
                { label: 'ポイント', value: 'point' },
                { label: '注意', value: 'caution' },
                { label: 'メモ', value: 'memo' },
                { label: '関連', value: 'related' },
                { label: 'おすすめ', value: 'recommend' }
            ];
            return el('div', {},
                el(InspectorControls, {},
                    el(PanelBody, { title: 'ボックス設定' },
                        el(SelectControl, { label: 'タイプ', value: a.type, options: types,
                            onChange: function(v) { props.setAttributes({ type: v }); } })
                    )
                ),
                el('div', { className: 'knt-box knt-box--' + a.type },
                    el(RichText, { tagName: 'div', className: 'knt-box__title', value: a.title, placeholder: 'タイトル',
                        onChange: function(v) { props.setAttributes({ title: v }); } }),
                    el(RichText, { tagName: 'div', className: 'knt-box__content', value: a.content, placeholder: '内容を入力...',
                        onChange: function(v) { props.setAttributes({ content: v }); } })
                )
            );
        },
        save: function(props) {
            var a = props.attributes;
            return el('div', { className: 'knt-box knt-box--' + a.type },
                a.title ? el(RichText.Content, { tagName: 'div', className: 'knt-box__title', value: a.title }) : null,
                el(RichText.Content, { tagName: 'div', className: 'knt-box__content', value: a.content })
            );
        }
    });

    // ========================================
    // 4. ブログカード (knt/blog-card) — server-side rendered
    // ========================================
    registerBlockType('knt/blog-card', {
    apiVersion: 3,
        title: 'ブログカード',
        icon: 'admin-links',
        category: CATEGORY,
        attributes: {
            url: { type: 'string', default: '' },
            postId: { type: 'number', default: 0 }
        },
        edit: function(props) {
            return el('div', { className: 'knt-blog-card-editor', style: { padding: '16px', border: '1px dashed #ccc', borderRadius: '8px' } },
                el(TextControl, { label: '記事URL（内部リンク）', value: props.attributes.url, placeholder: 'https://...',
                    onChange: function(v) { props.setAttributes({ url: v }); } }),
                el('p', { style: { fontSize: '12px', color: '#888' } }, 'サイト内の記事URLを入力すると、自動でカード表示されます。')
            );
        },
        save: function() { return null; } // server-side
    });

    // ========================================
    // 5. CTAセクション (knt/cta-section)
    // ========================================
    registerBlockType('knt/cta-section', {
    apiVersion: 3,
        title: 'CTAセクション',
        icon: 'megaphone',
        category: CATEGORY,
        attributes: {
            title: { type: 'string', default: '' },
            description: { type: 'string', default: '' },
            buttonText: { type: 'string', default: '' },
            buttonUrl: { type: 'string', default: '' },
            bgColor: { type: 'string', default: '#C9553E' }
        },
        edit: function(props) {
            var a = props.attributes;
            return el('div', {},
                el(InspectorControls, {},
                    el(PanelBody, { title: 'CTA設定' },
                        el(TextControl, { label: 'ボタンURL', value: a.buttonUrl,
                            onChange: function(v) { props.setAttributes({ buttonUrl: v }); } }),
                        el(TextControl, { label: '背景色', value: a.bgColor,
                            onChange: function(v) { props.setAttributes({ bgColor: v }); } })
                    )
                ),
                el('div', { className: 'knt-cta-section', style: { background: a.bgColor, padding: '40px 24px', borderRadius: '16px', textAlign: 'center', color: '#fff' } },
                    el(RichText, { tagName: 'h3', value: a.title, placeholder: 'CTAタイトル', style: { color: '#fff' },
                        onChange: function(v) { props.setAttributes({ title: v }); } }),
                    el(RichText, { tagName: 'p', value: a.description, placeholder: '説明文', style: { color: 'rgba(255,255,255,0.9)' },
                        onChange: function(v) { props.setAttributes({ description: v }); } }),
                    el(RichText, { tagName: 'span', className: 'knt-cta-section__btn', value: a.buttonText, placeholder: 'ボタンテキスト',
                        onChange: function(v) { props.setAttributes({ buttonText: v }); } })
                )
            );
        },
        save: function(props) {
            var a = props.attributes;
            return el('div', { className: 'knt-cta-section', style: { background: a.bgColor } },
                el(RichText.Content, { tagName: 'h3', className: 'knt-cta-section__title', value: a.title }),
                el(RichText.Content, { tagName: 'p', className: 'knt-cta-section__desc', value: a.description }),
                el('a', { href: a.buttonUrl, className: 'knt-cta-section__btn', target: '_blank', rel: 'noopener noreferrer' },
                    el(RichText.Content, { tagName: 'span', value: a.buttonText })
                )
            );
        }
    });

    // ========================================
    // 6. アラートボックス (knt/alert)
    // ========================================
    registerBlockType('knt/alert', {
    apiVersion: 3,
        title: 'アラートボックス',
        icon: 'warning',
        category: CATEGORY,
        attributes: {
            type: { type: 'string', default: 'info' },
            text: { type: 'string', default: '' }
        },
        edit: function(props) {
            var a = props.attributes;
            return el('div', {},
                el(InspectorControls, {},
                    el(PanelBody, { title: 'アラート設定' },
                        el(SelectControl, { label: 'タイプ', value: a.type, options: [
                            { label: '情報', value: 'info' },
                            { label: '成功', value: 'success' },
                            { label: '注意', value: 'warning' },
                            { label: 'エラー', value: 'error' },
                            { label: '閉店', value: 'closed' }
                        ], onChange: function(v) { props.setAttributes({ type: v }); } })
                    )
                ),
                el('div', { className: 'knt-alert knt-alert--' + a.type },
                    el(RichText, { tagName: 'p', value: a.text, placeholder: 'アラートメッセージを入力...',
                        onChange: function(v) { props.setAttributes({ text: v }); } })
                )
            );
        },
        save: function(props) {
            var a = props.attributes;
            return el('div', { className: 'knt-alert knt-alert--' + a.type },
                el(RichText.Content, { tagName: 'p', value: a.text })
            );
        }
    });

    // ========================================
    // 7. セクションヘッダー (knt/section-header)
    // ========================================
    registerBlockType('knt/section-header', {
    apiVersion: 3,
        title: 'セクションヘッダー',
        icon: 'heading',
        category: CATEGORY,
        attributes: {
            title: { type: 'string', default: '' },
            subtitle: { type: 'string', default: '' }
        },
        edit: function(props) {
            var a = props.attributes;
            return el('div', { className: 'knt-section-header', style: { textAlign: 'center', padding: '24px 0' } },
                el(RichText, { tagName: 'h2', className: 'knt-section-header__title', value: a.title, placeholder: 'セクションタイトル',
                    onChange: function(v) { props.setAttributes({ title: v }); } }),
                el(RichText, { tagName: 'p', className: 'knt-section-header__subtitle', value: a.subtitle, placeholder: 'サブタイトル',
                    onChange: function(v) { props.setAttributes({ subtitle: v }); } })
            );
        },
        save: function(props) {
            var a = props.attributes;
            return el('div', { className: 'knt-section-header' },
                el(RichText.Content, { tagName: 'h2', className: 'knt-section-header__title', value: a.title }),
                a.subtitle ? el(RichText.Content, { tagName: 'p', className: 'knt-section-header__subtitle', value: a.subtitle }) : null
            );
        }
    });

    // ========================================
    // 8. 特徴ボックス (knt/feature-box)
    // ========================================
    registerBlockType('knt/feature-box', {
    apiVersion: 3,
        title: '特徴ボックス',
        icon: 'star-filled',
        category: CATEGORY,
        attributes: {
            icon: { type: 'string', default: '' },
            title: { type: 'string', default: '' },
            description: { type: 'string', default: '' }
        },
        edit: function(props) {
            var a = props.attributes;
            return el('div', { className: 'knt-feature-box', style: { textAlign: 'center', padding: '24px', border: '1px solid #eee', borderRadius: '12px' } },
                el(TextControl, { value: a.icon, placeholder: 'アイコン文字（例: 🍣）', style: { textAlign: 'center', fontSize: '32px' },
                    onChange: function(v) { props.setAttributes({ icon: v }); } }),
                el(RichText, { tagName: 'h3', value: a.title, placeholder: 'タイトル',
                    onChange: function(v) { props.setAttributes({ title: v }); } }),
                el(RichText, { tagName: 'p', value: a.description, placeholder: '説明文',
                    onChange: function(v) { props.setAttributes({ description: v }); } })
            );
        },
        save: function(props) {
            var a = props.attributes;
            return el('div', { className: 'knt-feature-box' },
                a.icon ? el('div', { className: 'knt-feature-box__icon' }, a.icon) : null,
                el(RichText.Content, { tagName: 'h3', className: 'knt-feature-box__title', value: a.title }),
                el(RichText.Content, { tagName: 'p', className: 'knt-feature-box__desc', value: a.description })
            );
        }
    });

    // ========================================
    // 9. 仕切り線 (knt/divider)
    // ========================================
    registerBlockType('knt/divider', {
    apiVersion: 3,
        title: '仕切り線',
        icon: 'minus',
        category: CATEGORY,
        attributes: {
            style: { type: 'string', default: 'dots' }
        },
        edit: function(props) {
            return el('div', {},
                el(InspectorControls, {},
                    el(PanelBody, { title: 'スタイル' },
                        el(SelectControl, { value: props.attributes.style, options: [
                            { label: 'ドット', value: 'dots' },
                            { label: 'ライン', value: 'line' },
                            { label: 'ウェーブ', value: 'wave' }
                        ], onChange: function(v) { props.setAttributes({ style: v }); } })
                    )
                ),
                el('hr', { className: 'knt-divider knt-divider--' + props.attributes.style })
            );
        },
        save: function(props) {
            return el('hr', { className: 'knt-divider knt-divider--' + props.attributes.style });
        }
    });

    // ========================================
    // 10. 見出しブロック 高機能 (knt/heading-decorated)
    // ========================================
    registerBlockType('knt/heading-decorated', {
    apiVersion: 3,
        title: '見出しブロック（高機能）',
        icon: 'heading',
        category: CATEGORY,
        attributes: {
            text: { type: 'string', default: '' },
            level: { type: 'number', default: 2 },
            decoration: { type: 'string', default: 'underline' }
        },
        edit: function(props) {
            var a = props.attributes;
            return el('div', {},
                el(InspectorControls, {},
                    el(PanelBody, { title: '見出し設定' },
                        el(SelectControl, { label: 'レベル', value: String(a.level), options: [
                            { label: 'H2', value: '2' }, { label: 'H3', value: '3' }, { label: 'H4', value: '4' }
                        ], onChange: function(v) { props.setAttributes({ level: parseInt(v) }); } }),
                        el(SelectControl, { label: '装飾', value: a.decoration, options: [
                            { label: '下線', value: 'underline' },
                            { label: '左ボーダー', value: 'left-border' },
                            { label: '背景付き', value: 'bg' },
                            { label: 'アイコン付き', value: 'icon' }
                        ], onChange: function(v) { props.setAttributes({ decoration: v }); } })
                    )
                ),
                el('div', { className: 'knt-heading-decorated knt-heading-decorated--' + a.decoration },
                    el(RichText, { tagName: 'h' + a.level, value: a.text, placeholder: '見出しを入力...',
                        onChange: function(v) { props.setAttributes({ text: v }); } })
                )
            );
        },
        save: function(props) {
            var a = props.attributes;
            return el('div', { className: 'knt-heading-decorated knt-heading-decorated--' + a.decoration },
                el(RichText.Content, { tagName: 'h' + a.level, value: a.text })
            );
        }
    });

})(window.wp);
