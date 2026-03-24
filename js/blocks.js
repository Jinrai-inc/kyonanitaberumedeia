/**
 * KNT Media - Custom Gutenberg Blocks
 * No JSX, No build tools required.
 */
(function () {
  'use strict';

  var el = wp.element.createElement;
  var Fragment = wp.element.Fragment;
  var registerBlockType = wp.blocks.registerBlockType;
  var InspectorControls = wp.blockEditor.InspectorControls;
  var RichText = wp.blockEditor.RichText;
  var MediaUpload = wp.blockEditor.MediaUpload;
  var InnerBlocks = wp.blockEditor.InnerBlocks;
  var PanelBody = wp.components.PanelBody;
  var TextControl = wp.components.TextControl;
  var SelectControl = wp.components.SelectControl;
  var ToggleControl = wp.components.ToggleControl;
  var RangeControl = wp.components.RangeControl;
  var Button = wp.components.Button;
  var ColorPalette = wp.components.ColorPalette;

  var COLORS = [
    { name: 'テラコッタ', color: '#C9553E' },
    { name: 'ベージュ', color: '#F4F0EB' },
    { name: 'セカンダリ', color: '#EDE8E1' },
    { name: 'インク', color: '#2A2622' },
    { name: 'ホワイト', color: '#ffffff' },
    { name: 'アクセント淡色', color: '#FAEAE6' },
  ];

  /* ========================================
     1. KNT Section Block
     ======================================== */
  registerBlockType('knt/section', {
    title: 'セクション',
    description: '背景色・画像付きのセクションラッパー',
    icon: 'layout',
    category: 'knt-media',
    attributes: {
      backgroundColor: { type: 'string', default: '' },
      backgroundImage: { type: 'string', default: '' },
      backgroundImageId: { type: 'number', default: 0 },
      maxWidth: { type: 'number', default: 1100 },
      paddingTop: { type: 'number', default: 60 },
      paddingBottom: { type: 'number', default: 60 },
    },
    edit: function (props) {
      var attrs = props.attributes;
      var style = {
        backgroundColor: attrs.backgroundColor || undefined,
        backgroundImage: attrs.backgroundImage ? 'url(' + attrs.backgroundImage + ')' : undefined,
        backgroundSize: 'cover',
        backgroundPosition: 'center',
        paddingTop: attrs.paddingTop + 'px',
        paddingBottom: attrs.paddingBottom + 'px',
      };
      var innerStyle = { maxWidth: attrs.maxWidth + 'px', margin: '0 auto' };

      return el(Fragment, null,
        el(InspectorControls, null,
          el(PanelBody, { title: '背景設定' },
            el('p', null, '背景色'),
            el(ColorPalette, {
              colors: COLORS,
              value: attrs.backgroundColor,
              onChange: function (val) { props.setAttributes({ backgroundColor: val }); }
            }),
            el(MediaUpload, {
              onSelect: function (media) {
                props.setAttributes({ backgroundImage: media.url, backgroundImageId: media.id });
              },
              allowedTypes: ['image'],
              render: function (obj) {
                return el(Button, { onClick: obj.open, variant: 'secondary' },
                  attrs.backgroundImage ? '背景画像を変更' : '背景画像を選択'
                );
              }
            }),
            attrs.backgroundImage && el(Button, {
              onClick: function () { props.setAttributes({ backgroundImage: '', backgroundImageId: 0 }); },
              variant: 'link', isDestructive: true, style: { marginTop: '8px' }
            }, '画像を削除')
          ),
          el(PanelBody, { title: 'レイアウト' },
            el(RangeControl, { label: 'コンテンツ幅 (px)', value: attrs.maxWidth, onChange: function (v) { props.setAttributes({ maxWidth: v }); }, min: 600, max: 1400 }),
            el(RangeControl, { label: '上パディング (px)', value: attrs.paddingTop, onChange: function (v) { props.setAttributes({ paddingTop: v }); }, min: 0, max: 200 }),
            el(RangeControl, { label: '下パディング (px)', value: attrs.paddingBottom, onChange: function (v) { props.setAttributes({ paddingBottom: v }); }, min: 0, max: 200 })
          )
        ),
        el('div', { className: 'knt-section', style: style },
          el('div', { style: innerStyle },
            el(InnerBlocks, null)
          )
        )
      );
    },
    save: function (props) {
      var attrs = props.attributes;
      var style = {
        backgroundColor: attrs.backgroundColor || undefined,
        backgroundImage: attrs.backgroundImage ? 'url(' + attrs.backgroundImage + ')' : undefined,
        backgroundSize: 'cover',
        backgroundPosition: 'center',
        paddingTop: attrs.paddingTop + 'px',
        paddingBottom: attrs.paddingBottom + 'px',
      };
      return el('div', { className: 'knt-section', style: style },
        el('div', { style: { maxWidth: attrs.maxWidth + 'px', margin: '0 auto', padding: '0 20px' } },
          el(InnerBlocks.Content, null)
        )
      );
    }
  });

  /* ========================================
     2. KNT Button Block
     ======================================== */
  registerBlockType('knt/button', {
    title: 'ボタン',
    description: 'テーマスタイルのCTAボタン',
    icon: 'button',
    category: 'knt-media',
    attributes: {
      text: { type: 'string', default: 'ボタン' },
      url: { type: 'string', default: '' },
      style: { type: 'string', default: 'primary' },
      size: { type: 'string', default: 'medium' },
      newTab: { type: 'boolean', default: false },
      align: { type: 'string', default: 'center' },
    },
    edit: function (props) {
      var attrs = props.attributes;
      var cls = 'knt-btn knt-btn--' + attrs.style + ' knt-btn--' + attrs.size;

      return el(Fragment, null,
        el(InspectorControls, null,
          el(PanelBody, { title: 'ボタン設定' },
            el(TextControl, { label: 'リンクURL', value: attrs.url, onChange: function (v) { props.setAttributes({ url: v }); } }),
            el(SelectControl, { label: 'スタイル', value: attrs.style, options: [
              { label: 'プライマリ（塗り）', value: 'primary' },
              { label: 'セカンダリ（枠線）', value: 'secondary' }
            ], onChange: function (v) { props.setAttributes({ style: v }); } }),
            el(SelectControl, { label: 'サイズ', value: attrs.size, options: [
              { label: '小', value: 'small' },
              { label: '中', value: 'medium' },
              { label: '大', value: 'large' }
            ], onChange: function (v) { props.setAttributes({ size: v }); } }),
            el(SelectControl, { label: '配置', value: attrs.align, options: [
              { label: '左', value: 'left' },
              { label: '中央', value: 'center' },
              { label: '右', value: 'right' }
            ], onChange: function (v) { props.setAttributes({ align: v }); } }),
            el(ToggleControl, { label: '新しいタブで開く', checked: attrs.newTab, onChange: function (v) { props.setAttributes({ newTab: v }); } })
          )
        ),
        el('div', { style: { textAlign: attrs.align } },
          el(RichText, {
            tagName: 'span',
            className: cls,
            value: attrs.text,
            onChange: function (v) { props.setAttributes({ text: v }); },
            placeholder: 'ボタンテキスト',
          })
        )
      );
    },
    save: function (props) {
      var attrs = props.attributes;
      var cls = 'knt-btn knt-btn--' + attrs.style + ' knt-btn--' + attrs.size;
      var linkAttrs = { className: cls, href: attrs.url || '#' };
      if (attrs.newTab) { linkAttrs.target = '_blank'; linkAttrs.rel = 'noopener noreferrer'; }
      return el('div', { className: 'knt-btn-wrapper', style: { textAlign: attrs.align } },
        el('a', linkAttrs, el(RichText.Content, { value: attrs.text }))
      );
    }
  });

  /* ========================================
     3. KNT FAQ Block
     ======================================== */
  registerBlockType('knt/faq', {
    title: 'FAQ（よくある質問）',
    description: 'アコーディオン式FAQ。FAQPage構造化データを自動出力',
    icon: 'editor-help',
    category: 'knt-media',
    attributes: {
      items: { type: 'array', default: [] },
    },
    edit: function (props) {
      var items = props.attributes.items;

      function updateItem(index, key, value) {
        var newItems = items.slice();
        newItems[index] = Object.assign({}, newItems[index]);
        newItems[index][key] = value;
        props.setAttributes({ items: newItems });
      }

      function addItem() {
        props.setAttributes({ items: items.concat([{ question: '', answer: '' }]) });
      }

      function removeItem(index) {
        var newItems = items.slice();
        newItems.splice(index, 1);
        props.setAttributes({ items: newItems });
      }

      return el('div', { className: 'knt-faq knt-faq--editor' },
        el('div', { className: 'knt-faq__header' }, el('strong', null, 'FAQ（よくある質問）')),
        items.map(function (item, i) {
          return el('div', { key: i, className: 'knt-faq__item knt-faq__item--editor' },
            el('div', { style: { display: 'flex', alignItems: 'center', gap: '8px', marginBottom: '8px' } },
              el('span', { className: 'knt-faq__q-label' }, 'Q'),
              el(TextControl, {
                value: item.question || '',
                onChange: function (v) { updateItem(i, 'question', v); },
                placeholder: '質問を入力',
                style: { flex: 1, margin: 0 },
              })
            ),
            el('div', { style: { display: 'flex', alignItems: 'flex-start', gap: '8px', marginBottom: '8px' } },
              el('span', { className: 'knt-faq__a-label' }, 'A'),
              el(RichText, {
                tagName: 'div',
                value: item.answer || '',
                onChange: function (v) { updateItem(i, 'answer', v); },
                placeholder: '回答を入力',
                style: { flex: 1 },
              })
            ),
            el(Button, { onClick: function () { removeItem(i); }, variant: 'link', isDestructive: true, isSmall: true }, '削除')
          );
        }),
        el(Button, { onClick: addItem, variant: 'secondary', style: { marginTop: '12px' } }, '+ 質問を追加')
      );
    },
    save: function () {
      // Server-side rendered
      return null;
    }
  });

  /* ========================================
     4. KNT Callout Block
     ======================================== */
  registerBlockType('knt/callout', {
    title: 'コールアウト',
    description: '注意・ヒント・情報ボックス',
    icon: 'info-outline',
    category: 'knt-media',
    attributes: {
      type: { type: 'string', default: 'info' },
      title: { type: 'string', default: '' },
      content: { type: 'string', default: '' },
    },
    edit: function (props) {
      var attrs = props.attributes;
      var icons = { info: 'ℹ️', tip: '💡', warning: '⚠️', note: '📝' };
      return el(Fragment, null,
        el(InspectorControls, null,
          el(PanelBody, { title: 'コールアウト設定' },
            el(SelectControl, { label: 'タイプ', value: attrs.type, options: [
              { label: '情報（Info）', value: 'info' },
              { label: 'ヒント（Tip）', value: 'tip' },
              { label: '注意（Warning）', value: 'warning' },
              { label: 'メモ（Note）', value: 'note' }
            ], onChange: function (v) { props.setAttributes({ type: v }); } })
          )
        ),
        el('div', { className: 'knt-callout knt-callout--' + attrs.type },
          el('div', { className: 'knt-callout__icon' }, icons[attrs.type] || 'ℹ️'),
          el('div', { className: 'knt-callout__body' },
            el(RichText, { tagName: 'div', className: 'knt-callout__title', value: attrs.title, onChange: function (v) { props.setAttributes({ title: v }); }, placeholder: 'タイトル（任意）' }),
            el(RichText, { tagName: 'div', className: 'knt-callout__content', value: attrs.content, onChange: function (v) { props.setAttributes({ content: v }); }, placeholder: '内容を入力...' })
          )
        )
      );
    },
    save: function (props) {
      var attrs = props.attributes;
      var icons = { info: 'ℹ️', tip: '💡', warning: '⚠️', note: '📝' };
      return el('div', { className: 'knt-callout knt-callout--' + attrs.type },
        el('div', { className: 'knt-callout__icon' }, icons[attrs.type] || 'ℹ️'),
        el('div', { className: 'knt-callout__body' },
          attrs.title && el(RichText.Content, { tagName: 'div', className: 'knt-callout__title', value: attrs.title }),
          el(RichText.Content, { tagName: 'div', className: 'knt-callout__content', value: attrs.content })
        )
      );
    }
  });

  /* ========================================
     5. KNT Profile Block
     ======================================== */
  registerBlockType('knt/profile', {
    title: 'プロフィール',
    description: '執筆者・スタッフ紹介カード',
    icon: 'admin-users',
    category: 'knt-media',
    attributes: {
      imageUrl: { type: 'string', default: '' },
      imageId: { type: 'number', default: 0 },
      name: { type: 'string', default: '' },
      role: { type: 'string', default: '' },
      description: { type: 'string', default: '' },
      twitter: { type: 'string', default: '' },
      instagram: { type: 'string', default: '' },
    },
    edit: function (props) {
      var attrs = props.attributes;
      return el(Fragment, null,
        el(InspectorControls, null,
          el(PanelBody, { title: 'SNSリンク' },
            el(TextControl, { label: 'X (Twitter) URL', value: attrs.twitter, onChange: function (v) { props.setAttributes({ twitter: v }); } }),
            el(TextControl, { label: 'Instagram URL', value: attrs.instagram, onChange: function (v) { props.setAttributes({ instagram: v }); } })
          )
        ),
        el('div', { className: 'knt-profile' },
          el('div', { className: 'knt-profile__avatar' },
            el(MediaUpload, {
              onSelect: function (media) { props.setAttributes({ imageUrl: media.url, imageId: media.id }); },
              allowedTypes: ['image'],
              render: function (obj) {
                return attrs.imageUrl
                  ? el('img', { src: attrs.imageUrl, onClick: obj.open, style: { cursor: 'pointer' } })
                  : el(Button, { onClick: obj.open, variant: 'secondary' }, '写真を選択');
              }
            })
          ),
          el('div', { className: 'knt-profile__body' },
            el(RichText, { tagName: 'div', className: 'knt-profile__name', value: attrs.name, onChange: function (v) { props.setAttributes({ name: v }); }, placeholder: '名前' }),
            el(RichText, { tagName: 'div', className: 'knt-profile__role', value: attrs.role, onChange: function (v) { props.setAttributes({ role: v }); }, placeholder: '役割・肩書き' }),
            el(RichText, { tagName: 'div', className: 'knt-profile__desc', value: attrs.description, onChange: function (v) { props.setAttributes({ description: v }); }, placeholder: '自己紹介...' })
          )
        )
      );
    },
    save: function (props) {
      var attrs = props.attributes;
      return el('div', { className: 'knt-profile' },
        el('div', { className: 'knt-profile__avatar' },
          attrs.imageUrl && el('img', { src: attrs.imageUrl, alt: attrs.name || '' })
        ),
        el('div', { className: 'knt-profile__body' },
          el(RichText.Content, { tagName: 'div', className: 'knt-profile__name', value: attrs.name }),
          attrs.role && el(RichText.Content, { tagName: 'div', className: 'knt-profile__role', value: attrs.role }),
          el(RichText.Content, { tagName: 'div', className: 'knt-profile__desc', value: attrs.description }),
          (attrs.twitter || attrs.instagram) && el('div', { className: 'knt-profile__social' },
            attrs.twitter && el('a', { href: attrs.twitter, target: '_blank', rel: 'noopener noreferrer' }, 'X'),
            attrs.instagram && el('a', { href: attrs.instagram, target: '_blank', rel: 'noopener noreferrer' }, 'Instagram')
          )
        )
      );
    }
  });

  /* ========================================
     6. KNT Star Rating Block
     ======================================== */
  registerBlockType('knt/rating', {
    title: '星レーティング',
    description: 'レストランの味・コスパなどの星評価',
    icon: 'star-filled',
    category: 'knt-media',
    attributes: {
      label: { type: 'string', default: '味' },
      rating: { type: 'number', default: 3 },
    },
    edit: function (props) {
      var attrs = props.attributes;
      var stars = '';
      for (var i = 1; i <= 5; i++) { stars += i <= attrs.rating ? '★' : '☆'; }
      return el(Fragment, null,
        el(InspectorControls, null,
          el(PanelBody, { title: 'レーティング設定' },
            el(TextControl, { label: 'ラベル', value: attrs.label, onChange: function (v) { props.setAttributes({ label: v }); } }),
            el(RangeControl, { label: '評価', value: attrs.rating, onChange: function (v) { props.setAttributes({ rating: v }); }, min: 1, max: 5 })
          )
        ),
        el('div', { className: 'knt-rating' },
          el('span', { className: 'knt-rating__label' }, attrs.label),
          el('span', { className: 'knt-rating__stars' }, stars)
        )
      );
    },
    save: function (props) {
      var attrs = props.attributes;
      var stars = '';
      for (var i = 1; i <= 5; i++) { stars += i <= attrs.rating ? '★' : '☆'; }
      return el('div', { className: 'knt-rating' },
        el('span', { className: 'knt-rating__label' }, attrs.label),
        el('span', { className: 'knt-rating__stars' }, stars)
      );
    }
  });

  /* ========================================
     7. KNT Steps Block
     ======================================== */
  registerBlockType('knt/steps', {
    title: 'ステップ・手順',
    description: '番号付きのタイムライン形式の手順ガイド',
    icon: 'editor-ol',
    category: 'knt-media',
    attributes: {
      steps: { type: 'array', default: [] },
    },
    edit: function (props) {
      var steps = props.attributes.steps;

      function updateStep(index, key, value) {
        var newSteps = steps.slice();
        newSteps[index] = Object.assign({}, newSteps[index]);
        newSteps[index][key] = value;
        props.setAttributes({ steps: newSteps });
      }

      function addStep() {
        props.setAttributes({ steps: steps.concat([{ title: '', description: '' }]) });
      }

      function removeStep(index) {
        var newSteps = steps.slice();
        newSteps.splice(index, 1);
        props.setAttributes({ steps: newSteps });
      }

      return el('div', { className: 'knt-steps knt-steps--editor' },
        el('div', { className: 'knt-steps__header' }, el('strong', null, 'ステップ・手順')),
        steps.map(function (step, i) {
          return el('div', { key: i, className: 'knt-steps__item' },
            el('div', { className: 'knt-steps__number' }, i + 1),
            el('div', { className: 'knt-steps__content' },
              el(TextControl, { value: step.title || '', onChange: function (v) { updateStep(i, 'title', v); }, placeholder: 'ステップタイトル' }),
              el(RichText, { tagName: 'div', value: step.description || '', onChange: function (v) { updateStep(i, 'description', v); }, placeholder: '説明を入力...' }),
              el(Button, { onClick: function () { removeStep(i); }, variant: 'link', isDestructive: true, isSmall: true }, '削除')
            )
          );
        }),
        el(Button, { onClick: addStep, variant: 'secondary', style: { marginTop: '12px' } }, '+ ステップを追加')
      );
    },
    save: function (props) {
      var steps = props.attributes.steps;
      return el('div', { className: 'knt-steps' },
        steps.map(function (step, i) {
          return el('div', { key: i, className: 'knt-steps__item' },
            el('div', { className: 'knt-steps__number' }, i + 1),
            el('div', { className: 'knt-steps__content' },
              step.title && el('div', { className: 'knt-steps__title' }, step.title),
              el(RichText.Content, { tagName: 'div', className: 'knt-steps__desc', value: step.description || '' })
            )
          );
        })
      );
    }
  });

  /* ========================================
     8. KNT Price Table Block
     ======================================== */
  registerBlockType('knt/price-table', {
    title: '料金表',
    description: 'プラン比較の料金テーブル',
    icon: 'money-alt',
    category: 'knt-media',
    attributes: {
      plans: { type: 'array', default: [] },
    },
    edit: function (props) {
      var plans = props.attributes.plans;

      function updatePlan(index, key, value) {
        var newPlans = plans.slice();
        newPlans[index] = Object.assign({}, newPlans[index]);
        newPlans[index][key] = value;
        props.setAttributes({ plans: newPlans });
      }

      function addPlan() {
        props.setAttributes({ plans: plans.concat([{ name: '', price: '', features: '', buttonText: '', buttonUrl: '', highlighted: false }]) });
      }

      function removePlan(index) {
        var newPlans = plans.slice();
        newPlans.splice(index, 1);
        props.setAttributes({ plans: newPlans });
      }

      return el('div', { className: 'knt-price-table--editor' },
        el('strong', null, '料金表'),
        plans.map(function (plan, i) {
          return el('div', { key: i, style: { border: '1px solid #ddd', borderRadius: '8px', padding: '16px', marginTop: '12px' } },
            el(TextControl, { label: 'プラン名', value: plan.name || '', onChange: function (v) { updatePlan(i, 'name', v); } }),
            el(TextControl, { label: '価格', value: plan.price || '', onChange: function (v) { updatePlan(i, 'price', v); }, placeholder: '¥980/月' }),
            el(TextControl, { label: '特徴（改行区切り）', value: plan.features || '', onChange: function (v) { updatePlan(i, 'features', v); }, placeholder: '特徴1\n特徴2\n特徴3' }),
            el(TextControl, { label: 'ボタンテキスト', value: plan.buttonText || '', onChange: function (v) { updatePlan(i, 'buttonText', v); } }),
            el(TextControl, { label: 'ボタンURL', value: plan.buttonUrl || '', onChange: function (v) { updatePlan(i, 'buttonUrl', v); } }),
            el(ToggleControl, { label: 'おすすめ（ハイライト）', checked: plan.highlighted || false, onChange: function (v) { updatePlan(i, 'highlighted', v); } }),
            el(Button, { onClick: function () { removePlan(i); }, variant: 'link', isDestructive: true, isSmall: true }, '削除')
          );
        }),
        el(Button, { onClick: addPlan, variant: 'secondary', style: { marginTop: '12px' } }, '+ プランを追加')
      );
    },
    save: function (props) {
      var plans = props.attributes.plans;
      return el('div', { className: 'knt-price-table' },
        plans.map(function (plan, i) {
          var cls = 'knt-price-table__plan' + (plan.highlighted ? ' knt-price-table__plan--highlight' : '');
          var features = (plan.features || '').split('\n').filter(function (f) { return f.trim(); });
          return el('div', { key: i, className: cls },
            plan.highlighted && el('div', { className: 'knt-price-table__badge' }, 'おすすめ'),
            el('div', { className: 'knt-price-table__name' }, plan.name),
            el('div', { className: 'knt-price-table__price' }, plan.price),
            el('ul', { className: 'knt-price-table__features' },
              features.map(function (f, j) { return el('li', { key: j }, f); })
            ),
            plan.buttonText && el('a', {
              href: plan.buttonUrl || '#',
              className: 'knt-btn knt-btn--' + (plan.highlighted ? 'primary' : 'secondary') + ' knt-btn--medium'
            }, plan.buttonText)
          );
        })
      );
    }
  });

  /* ========================================
     9. KNT App CTA Block
     ======================================== */
  registerBlockType('knt/app-cta', {
    title: 'アプリ訴求CTA',
    description: 'アプリへの誘導バナーブロック',
    icon: 'smartphone',
    category: 'knt-media',
    attributes: {
      title: { type: 'string', default: '今日なに食べる？で迷わない。' },
      description: { type: 'string', default: 'AIがあなたの気分にぴったりのお店を提案します。' },
      buttonText: { type: 'string', default: '無料ではじめる' },
      buttonUrl: { type: 'string', default: '' },
    },
    edit: function (props) {
      var attrs = props.attributes;
      return el(Fragment, null,
        el(InspectorControls, null,
          el(PanelBody, { title: 'リンク設定' },
            el(TextControl, { label: 'ボタンURL', value: attrs.buttonUrl, onChange: function (v) { props.setAttributes({ buttonUrl: v }); } })
          )
        ),
        el('div', { className: 'knt-app-cta' },
          el(RichText, { tagName: 'h3', className: 'knt-app-cta__title', value: attrs.title, onChange: function (v) { props.setAttributes({ title: v }); }, placeholder: 'タイトル' }),
          el(RichText, { tagName: 'p', className: 'knt-app-cta__desc', value: attrs.description, onChange: function (v) { props.setAttributes({ description: v }); }, placeholder: '説明文' }),
          el(RichText, { tagName: 'span', className: 'knt-btn knt-btn--primary knt-btn--large', value: attrs.buttonText, onChange: function (v) { props.setAttributes({ buttonText: v }); } })
        )
      );
    },
    save: function (props) {
      var attrs = props.attributes;
      return el('div', { className: 'knt-app-cta' },
        el(RichText.Content, { tagName: 'h3', className: 'knt-app-cta__title', value: attrs.title }),
        el(RichText.Content, { tagName: 'p', className: 'knt-app-cta__desc', value: attrs.description }),
        el('a', { href: attrs.buttonUrl || '#', className: 'knt-btn knt-btn--primary knt-btn--large', target: '_blank', rel: 'noopener noreferrer' },
          el(RichText.Content, { value: attrs.buttonText })
        )
      );
    }
  });

  /* ========================================
     10. KNT Restaurant Card Block
     ======================================== */
  registerBlockType('knt/restaurant-card', {
    title: 'レストランカード',
    description: 'お店紹介カード（画像・ジャンル・評価付き）',
    icon: 'store',
    category: 'knt-media',
    attributes: {
      imageUrl: { type: 'string', default: '' },
      imageId: { type: 'number', default: 0 },
      name: { type: 'string', default: '' },
      genre: { type: 'string', default: '' },
      area: { type: 'string', default: '' },
      rating: { type: 'number', default: 3 },
      description: { type: 'string', default: '' },
      url: { type: 'string', default: '' },
    },
    edit: function (props) {
      var attrs = props.attributes;
      var stars = '';
      for (var i = 1; i <= 5; i++) { stars += i <= attrs.rating ? '★' : '☆'; }

      return el(Fragment, null,
        el(InspectorControls, null,
          el(PanelBody, { title: 'お店情報' },
            el(TextControl, { label: 'ジャンル', value: attrs.genre, onChange: function (v) { props.setAttributes({ genre: v }); }, placeholder: 'ラーメン' }),
            el(TextControl, { label: 'エリア', value: attrs.area, onChange: function (v) { props.setAttributes({ area: v }); }, placeholder: '渋谷' }),
            el(RangeControl, { label: '評価', value: attrs.rating, onChange: function (v) { props.setAttributes({ rating: v }); }, min: 1, max: 5 }),
            el(TextControl, { label: 'リンクURL', value: attrs.url, onChange: function (v) { props.setAttributes({ url: v }); } })
          )
        ),
        el('div', { className: 'knt-restaurant-card' },
          el('div', { className: 'knt-restaurant-card__image' },
            el(MediaUpload, {
              onSelect: function (media) { props.setAttributes({ imageUrl: media.url, imageId: media.id }); },
              allowedTypes: ['image'],
              render: function (obj) {
                return attrs.imageUrl
                  ? el('img', { src: attrs.imageUrl, onClick: obj.open, style: { cursor: 'pointer', width: '100%', borderRadius: '12px' } })
                  : el(Button, { onClick: obj.open, variant: 'secondary' }, '画像を選択');
              }
            })
          ),
          el('div', { className: 'knt-restaurant-card__body' },
            el('div', { className: 'knt-restaurant-card__tags' },
              attrs.genre && el('span', { className: 'cat-tag' }, attrs.genre),
              attrs.area && el('span', { className: 'knt-restaurant-card__area' }, attrs.area)
            ),
            el(RichText, { tagName: 'div', className: 'knt-restaurant-card__name', value: attrs.name, onChange: function (v) { props.setAttributes({ name: v }); }, placeholder: 'お店の名前' }),
            el('div', { className: 'knt-rating__stars' }, stars),
            el(RichText, { tagName: 'p', className: 'knt-restaurant-card__desc', value: attrs.description, onChange: function (v) { props.setAttributes({ description: v }); }, placeholder: 'お店の紹介文...' })
          )
        )
      );
    },
    save: function (props) {
      var attrs = props.attributes;
      var stars = '';
      for (var i = 1; i <= 5; i++) { stars += i <= attrs.rating ? '★' : '☆'; }
      var wrapper = attrs.url ? 'a' : 'div';
      var wrapperAttrs = { className: 'knt-restaurant-card' };
      if (attrs.url) {
        wrapperAttrs.href = attrs.url;
        wrapperAttrs.target = '_blank';
        wrapperAttrs.rel = 'noopener noreferrer';
      }
      return el(wrapper, wrapperAttrs,
        attrs.imageUrl && el('div', { className: 'knt-restaurant-card__image' },
          el('img', { src: attrs.imageUrl, alt: attrs.name || '' })
        ),
        el('div', { className: 'knt-restaurant-card__body' },
          el('div', { className: 'knt-restaurant-card__tags' },
            attrs.genre && el('span', { className: 'cat-tag' }, attrs.genre),
            attrs.area && el('span', { className: 'knt-restaurant-card__area' }, attrs.area)
          ),
          el(RichText.Content, { tagName: 'div', className: 'knt-restaurant-card__name', value: attrs.name }),
          el('div', { className: 'knt-rating__stars' }, stars),
          el(RichText.Content, { tagName: 'p', className: 'knt-restaurant-card__desc', value: attrs.description })
        )
      );
    }
  });

})();
