/**
 * KNT Media - Interactive Japan Map (realistic SVG + full municipalities)
 */
(function () {
  'use strict';

  /* ========================================
     Major City Data (renamed from JAPAN_DATA)
     主要市区町村チップ表示 + フォールバック用
     ======================================== */
  var MAJOR_CITY_DATA = {
    '01': { name: '北海道', cities: ['札幌市','函館市','旭川市','小樽市','帯広市','釧路市','北見市','苫小牧市','江別市','千歳市'] },
    '02': { name: '青森県', cities: ['青森市','弘前市','八戸市','十和田市','むつ市','五所川原市'] },
    '03': { name: '岩手県', cities: ['盛岡市','一関市','奥州市','花巻市','北上市','宮古市'] },
    '04': { name: '宮城県', cities: ['仙台市','石巻市','大崎市','名取市','登米市','気仙沼市'] },
    '05': { name: '秋田県', cities: ['秋田市','横手市','大仙市','由利本荘市','能代市','大館市'] },
    '06': { name: '山形県', cities: ['山形市','鶴岡市','酒田市','米沢市','天童市','東根市'] },
    '07': { name: '福島県', cities: ['福島市','郡山市','いわき市','会津若松市','須賀川市','白河市'] },
    '08': { name: '茨城県', cities: ['水戸市','つくば市','日立市','ひたちなか市','古河市','土浦市'] },
    '09': { name: '栃木県', cities: ['宇都宮市','小山市','栃木市','足利市','佐野市','那須塩原市'] },
    '10': { name: '群馬県', cities: ['前橋市','高崎市','太田市','伊勢崎市','桐生市','館林市'] },
    '11': { name: '埼玉県', cities: ['さいたま市','川越市','川口市','所沢市','越谷市','草加市','春日部市','上尾市','熊谷市','新座市'] },
    '12': { name: '千葉県', cities: ['千葉市','船橋市','松戸市','市川市','柏市','市原市','八千代市','流山市','浦安市','習志野市'] },
    '13': { name: '東京都', cities: ['千代田区','中央区','港区','新宿区','文京区','台東区','墨田区','江東区','品川区','目黒区','大田区','世田谷区','渋谷区','中野区','杉並区','豊島区','北区','荒川区','板橋区','練馬区','足立区','葛飾区','江戸川区','八王子市','立川市','武蔵野市','三鷹市','町田市'] },
    '14': { name: '神奈川県', cities: ['横浜市','川崎市','相模原市','藤沢市','横須賀市','平塚市','茅ヶ崎市','大和市','厚木市','小田原市','鎌倉市'] },
    '15': { name: '新潟県', cities: ['新潟市','長岡市','上越市','三条市','柏崎市','燕市'] },
    '16': { name: '富山県', cities: ['富山市','高岡市','射水市','南砺市','氷見市','砺波市'] },
    '17': { name: '石川県', cities: ['金沢市','白山市','小松市','加賀市','七尾市','野々市市'] },
    '18': { name: '福井県', cities: ['福井市','坂井市','越前市','敦賀市','鯖江市','大野市'] },
    '19': { name: '山梨県', cities: ['甲府市','甲斐市','南アルプス市','笛吹市','富士吉田市','北杜市'] },
    '20': { name: '長野県', cities: ['長野市','松本市','上田市','飯田市','佐久市','安曇野市'] },
    '21': { name: '岐阜県', cities: ['岐阜市','大垣市','各務原市','多治見市','高山市','可児市'] },
    '22': { name: '静岡県', cities: ['静岡市','浜松市','富士市','沼津市','磐田市','藤枝市','焼津市','三島市'] },
    '23': { name: '愛知県', cities: ['名古屋市','豊田市','岡崎市','一宮市','豊橋市','春日井市','安城市','豊川市','刈谷市','小牧市'] },
    '24': { name: '三重県', cities: ['津市','四日市市','鈴鹿市','松阪市','伊勢市','桑名市'] },
    '25': { name: '滋賀県', cities: ['大津市','草津市','長浜市','東近江市','彦根市','甲賀市'] },
    '26': { name: '京都府', cities: ['京都市','宇治市','亀岡市','舞鶴市','城陽市','長岡京市','福知山市'] },
    '27': { name: '大阪府', cities: ['大阪市','堺市','東大阪市','豊中市','枚方市','吹田市','高槻市','茨木市','八尾市','寝屋川市','岸和田市'] },
    '28': { name: '兵庫県', cities: ['神戸市','姫路市','尼崎市','西宮市','明石市','加古川市','宝塚市','伊丹市','川西市','芦屋市'] },
    '29': { name: '奈良県', cities: ['奈良市','橿原市','生駒市','大和郡山市','天理市','香芝市'] },
    '30': { name: '和歌山県', cities: ['和歌山市','田辺市','橋本市','紀の川市','岩出市','海南市'] },
    '31': { name: '鳥取県', cities: ['鳥取市','米子市','倉吉市','境港市','岩美町'] },
    '32': { name: '島根県', cities: ['松江市','出雲市','浜田市','益田市','大田市','安来市'] },
    '33': { name: '岡山県', cities: ['岡山市','倉敷市','津山市','総社市','笠岡市','玉野市'] },
    '34': { name: '広島県', cities: ['広島市','福山市','呉市','東広島市','尾道市','三原市','廿日市市'] },
    '35': { name: '山口県', cities: ['下関市','山口市','宇部市','周南市','岩国市','防府市'] },
    '36': { name: '徳島県', cities: ['徳島市','阿南市','鳴門市','吉野川市','小松島市','美馬市'] },
    '37': { name: '香川県', cities: ['高松市','丸亀市','三豊市','観音寺市','坂出市','さぬき市'] },
    '38': { name: '愛媛県', cities: ['松山市','今治市','新居浜市','西条市','四国中央市','宇和島市'] },
    '39': { name: '高知県', cities: ['高知市','南国市','四万十市','須崎市','土佐市','香南市'] },
    '40': { name: '福岡県', cities: ['福岡市','北九州市','久留米市','飯塚市','大牟田市','春日市','筑紫野市'] },
    '41': { name: '佐賀県', cities: ['佐賀市','唐津市','鳥栖市','伊万里市','武雄市','小城市'] },
    '42': { name: '長崎県', cities: ['長崎市','佐世保市','諫早市','大村市','島原市','五島市'] },
    '43': { name: '熊本県', cities: ['熊本市','八代市','天草市','玉名市','宇城市','合志市'] },
    '44': { name: '大分県', cities: ['大分市','別府市','中津市','佐伯市','日田市','宇佐市'] },
    '45': { name: '宮崎県', cities: ['宮崎市','都城市','延岡市','日向市','日南市','小林市'] },
    '46': { name: '鹿児島県', cities: ['鹿児島市','霧島市','鹿屋市','薩摩川内市','姶良市','奄美市'] },
    '47': { name: '沖縄県', cities: ['那覇市','沖縄市','うるま市','浦添市','宜野湾市','名護市','豊見城市','糸満市'] }
  };
  var state = {
    selectedPref: null,
    selectedCity: null,
    municipalityData: null,
    mediaQuery: null
  };
  var REGION_COLORS = {
    hokkaido: '#9ad17b',
    tohoku: '#86d6a8',
    kanto: '#f0b34a',
    chubu: '#f3c766',
    kinki: '#ef9c56',
    chugoku: '#f08b67',
    shikoku: '#e97970',
    kyushu: '#d96e78',
    okinawa: '#72c9c3'
  };
  var PREF_REGION_MAP = {
    '01': 'hokkaido',
    '02': 'tohoku', '03': 'tohoku', '04': 'tohoku', '05': 'tohoku', '06': 'tohoku', '07': 'tohoku',
    '08': 'kanto', '09': 'kanto', '10': 'kanto', '11': 'kanto', '12': 'kanto', '13': 'kanto', '14': 'kanto',
    '15': 'chubu', '16': 'chubu', '17': 'chubu', '18': 'chubu', '19': 'chubu', '20': 'chubu', '21': 'chubu', '22': 'chubu', '23': 'chubu', '24': 'chubu',
    '25': 'kinki', '26': 'kinki', '27': 'kinki', '28': 'kinki', '29': 'kinki', '30': 'kinki',
    '31': 'chugoku', '32': 'chugoku', '33': 'chugoku', '34': 'chugoku', '35': 'chugoku',
    '36': 'shikoku', '37': 'shikoku', '38': 'shikoku', '39': 'shikoku',
    '40': 'kyushu', '41': 'kyushu', '42': 'kyushu', '43': 'kyushu', '44': 'kyushu', '45': 'kyushu', '46': 'kyushu',
    '47': 'okinawa'
  };
  function normalizeCode(value) {
    return String(value || '').trim().padStart(2, '0').slice(-2);
  }
  function escapeHtml(value) {
    return String(value || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }
  function getThemeBasePath() {
    // wp_localize_script から取得（最も確実）
    if (typeof kntMapData !== 'undefined' && kntMapData.themeUrl) {
      console.log('[KNT Map] themeUrl:', kntMapData.themeUrl);
      return kntMapData.themeUrl.replace(/\/$/, '');
    }
    console.warn('[KNT Map] kntMapData not found, falling back to script src detection');
    // フォールバック: script src から推測
    var current = document.currentScript;
    if (current && current.src) {
      return current.src.replace(/\/js\/japan-map\.js(?:\?.*)?$/, '');
    }
    var scripts = document.querySelectorAll('script[src]');
    for (var i = 0; i < scripts.length; i++) {
      var src = scripts[i].getAttribute('src') || '';
      if (/\/js\/japan-map\.js(?:\?.*)?$/.test(src)) {
        return src.replace(/\/js\/japan-map\.js(?:\?.*)?$/, '');
      }
    }
    return '';
  }
  function getMunicipalityData(prefCode) {
    prefCode = normalizeCode(prefCode);
    if (state.municipalityData && state.municipalityData[prefCode]) {
      return state.municipalityData[prefCode];
    }
    if (typeof MAJOR_CITY_DATA !== 'undefined' && MAJOR_CITY_DATA[prefCode]) {
      return {
        name: MAJOR_CITY_DATA[prefCode].name,
        majorCities: MAJOR_CITY_DATA[prefCode].cities.slice(),
        municipalities: MAJOR_CITY_DATA[prefCode].cities.slice()
      };
    }
    return null;
  }
  function getPrefName(prefCode) {
    var data = getMunicipalityData(prefCode);
    return data ? data.name : '';
  }
  function createTooltip(wrapper) {
    var old = wrapper.querySelector('#map-tooltip');
    if (old) old.remove();
    var tooltip = document.createElement('div');
    tooltip.className = 'japan-map-tooltip';
    tooltip.id = 'map-tooltip';
    wrapper.appendChild(tooltip);
    return tooltip;
  }
  function loadMunicipalityData() {
    // wp_localize_script 経由でインライン渡し（CORS回避）
    if (typeof kntMapData !== 'undefined' && kntMapData.municipalities && Object.keys(kntMapData.municipalities).length > 0) {
      state.municipalityData = kntMapData.municipalities;
      console.log('[KNT Map] municipalities loaded inline:', Object.keys(state.municipalityData).length, 'prefectures');
    } else {
      console.log('[KNT Map] municipalities not available, using MAJOR_CITY_DATA fallback');
    }
  }
  function setNodeColor(prefNode, code) {
    var region = PREF_REGION_MAP[code] || 'chubu';
    var color = REGION_COLORS[region] || '#f0b34a';
    prefNode.setAttribute('data-code', code);
    prefNode.setAttribute('data-region', region);
    prefNode.classList.add('prefecture-node');
    prefNode.style.setProperty('--pref-fill', color);
    var shapes = prefNode.querySelectorAll('path, polygon, rect, circle, ellipse, polyline');
    if (!shapes.length && prefNode.matches('path, polygon, rect, circle, ellipse, polyline')) {
      shapes = [prefNode];
    }
    Array.prototype.forEach.call(shapes, function (shape) {
      shape.classList.add('prefecture-shape');
      shape.style.fill = color;
      shape.style.opacity = '1';
      shape.style.pointerEvents = 'all';
      shape.setAttribute('vector-effect', 'non-scaling-stroke');
      shape.setAttribute('draggable', 'false');
    });
  }
  function normalizeSvg(svg) {
    svg.classList.add('knt-japan-map-svg');
    svg.setAttribute('role', 'img');
    svg.setAttribute('aria-label', '日本地図');
    svg.setAttribute('draggable', 'false');

    // SVGにサイズ属性を明示（高さ0問題対策）
    if (!svg.getAttribute('width')) {
      svg.setAttribute('width', '100%');
    }
    if (!svg.getAttribute('height')) {
      svg.setAttribute('height', '100%');
    }
    svg.style.pointerEvents = 'auto';

    var prefNodes = svg.querySelectorAll('[data-code]');
    console.log('[KNT Map] Found', prefNodes.length, 'prefecture nodes');
    Array.prototype.forEach.call(prefNodes, function (node) {
      var code = normalizeCode(node.getAttribute('data-code'));
      setNodeColor(node, code);
      // pointer-events を明示
      node.style.pointerEvents = 'auto';
      node.style.cursor = 'pointer';
    });
    // 念のため data-code が直接 shape 側にあるケースも拾う
    var orphanShapes = svg.querySelectorAll('path[data-code], polygon[data-code], rect[data-code], circle[data-code], ellipse[data-code], polyline[data-code]');
    Array.prototype.forEach.call(orphanShapes, function (shape) {
      var code = normalizeCode(shape.getAttribute('data-code'));
      setNodeColor(shape, code);
    });
  }
  function initMap() {
    var wrapper = document.getElementById('japan-map');
    if (!wrapper) return;

    // PHPでインライン埋め込み済みのSVGを検出
    var svg = wrapper.querySelector('svg');
    if (svg) {
      console.log('[KNT Map] Inline SVG found, normalizing');
      normalizeSvg(svg);
    } else {
      console.error('[KNT Map] SVG not found in #japan-map');
    }

    createTooltip(wrapper);
    if (state.selectedPref) {
      highlightPref(state.selectedPref);
    }
  }
  function populatePrefSelect() {
    var select = document.getElementById('pref-select');
    if (!select) return;
    select.innerHTML = '<option value="">タップして選んでね</option>';
    var source = state.municipalityData || MAJOR_CITY_DATA;
    Object.keys(source).sort().forEach(function (code) {
      var entry = source[code];
      var name = entry.name || '';
      var option = document.createElement('option');
      option.value = code;
      option.textContent = name;
      select.appendChild(option);
    });
  }
  function updateCitySelect(prefCode) {
    var select = document.getElementById('city-select');
    if (!select) return;
    select.innerHTML = '';
    var data = getMunicipalityData(prefCode);
    if (!prefCode || !data) {
      select.innerHTML = '<option value="">まず都道府県を選んでね</option>';
      select.disabled = true;
      return;
    }
    select.disabled = false;
    var defaultOpt = document.createElement('option');
    defaultOpt.value = '';
    defaultOpt.textContent = '市区町村を選択';
    select.appendChild(defaultOpt);
    data.municipalities.forEach(function (name) {
      var option = document.createElement('option');
      option.value = name;
      option.textContent = name;
      select.appendChild(option);
    });
    if (state.selectedCity) {
      select.value = state.selectedCity;
    }
  }
  function updateCityPanel(prefCode) {
    var panel = document.getElementById('city-panel');
    if (!panel) return;
    var data = getMunicipalityData(prefCode);
    if (!prefCode || !data) {
      panel.innerHTML = '';
      panel.classList.remove('has-cities');
      return;
    }
    var majorCities = Array.isArray(data.majorCities) && data.majorCities.length
      ? data.majorCities
      : (Array.isArray(data.municipalities) ? data.municipalities.slice(0, 12) : []);
    var html = '<p class="japan-map-city-panel-title">' + escapeHtml(data.name) + ' の主要エリア</p>';
    html += '<div class="japan-map-city-tags">';
    majorCities.forEach(function (city) {
      var selectedClass = state.selectedCity === city ? ' is-selected' : '';
      html += '<button type="button" class="japan-map-city-tag' + selectedClass + '" data-city="' + escapeHtml(city) + '">' + escapeHtml(city) + '</button>';
    });
    html += '</div>';
    panel.innerHTML = html;
    panel.classList.add('has-cities');
  }
  function updateButton() {
    var btn = document.getElementById('area-search-btn');
    if (!btn) return;
    if (!state.selectedPref) {
      btn.classList.add('is-disabled');
      btn.href = '#';
      return;
    }
    btn.classList.remove('is-disabled');
    var prefCode = normalizeCode(state.selectedPref);
    var prefSlug = 'area-' + prefCode;
    var base = window.location.origin;

    if (state.selectedCity) {
      var citySlug = prefSlug + '-' + encodeURIComponent(state.selectedCity);
      btn.href = base + '/?category_name=' + encodeURIComponent(citySlug);
    } else {
      btn.href = base + '/?category_name=' + encodeURIComponent(prefSlug);
    }
  }
  function highlightPref(code) {
    var activeNodes = document.querySelectorAll('#japan-map [data-code].is-active, #japan-map .prefecture-shape.is-active');
    Array.prototype.forEach.call(activeNodes, function (el) {
      el.classList.remove('is-active');
    });
    if (!code) return;
    var node = document.querySelector('#japan-map [data-code="' + normalizeCode(code) + '"]');
    if (!node) return;
    node.classList.add('is-active');
    var shapes = node.querySelectorAll('.prefecture-shape');
    if (!shapes.length && node.classList.contains('prefecture-shape')) {
      shapes = [node];
    }
    Array.prototype.forEach.call(shapes, function (shape) {
      shape.classList.add('is-active');
    });
  }
  function syncPrefSelect(code) {
    var select = document.getElementById('pref-select');
    if (select) {
      select.value = code || '';
    }
  }
  function syncCitySelect(city) {
    var select = document.getElementById('city-select');
    if (select) {
      select.value = city || '';
    }
  }
  function selectPref(code) {
    code = code ? normalizeCode(code) : null;
    console.log('[KNT Map] selectPref:', code, '→', getPrefName(code));
    state.selectedPref = code;
    state.selectedCity = null;
    syncPrefSelect(code);
    highlightPref(code);
    updateCitySelect(code);
    updateCityPanel(code);
    syncCitySelect(null);
    updateButton();
  }
  function selectCity(city) {
    state.selectedCity = city || null;
    var tags = document.querySelectorAll('.japan-map-city-tag');
    Array.prototype.forEach.call(tags, function (tag) {
      tag.classList.toggle('is-selected', tag.getAttribute('data-city') === state.selectedCity);
    });
    syncCitySelect(state.selectedCity);
    updateButton();
  }
  // elementsFromPoint でSVG内の [data-code] を検出
  function findPrefNodeAt(x, y, wrapper) {
    var elements = document.elementsFromPoint(x, y);
    for (var i = 0; i < elements.length; i++) {
      var el = elements[i];
      if (el.getAttribute && el.getAttribute('data-code')) return el;
      var parent = el.parentNode;
      while (parent && parent !== wrapper) {
        if (parent.getAttribute && parent.getAttribute('data-code')) return parent;
        parent = parent.parentNode;
      }
    }
    return null;
  }

  function bindEvents() {
    var wrapper = document.getElementById('japan-map');
    if (!wrapper) return;
    wrapper.addEventListener('dragstart', function (e) {
      e.preventDefault();
    });
    wrapper.addEventListener('selectstart', function (e) {
      e.preventDefault();
    });
    wrapper.addEventListener('pointerdown', function (e) {
      if (e.target.closest('[data-code]')) {
        e.preventDefault();
      }
    });
    wrapper.addEventListener('click', function (e) {
      // elementsFromPoint で直下の要素を正確に検出（SVG要素でclosest/target問題を回避）
      var prefNode = null;
      var elements = document.elementsFromPoint(e.clientX, e.clientY);
      for (var i = 0; i < elements.length; i++) {
        var el = elements[i];
        if (el.getAttribute && el.getAttribute('data-code')) {
          prefNode = el;
          break;
        }
        // 親をたどる（polygon → g[data-code]）
        var parent = el.parentNode;
        while (parent && parent !== wrapper) {
          if (parent.getAttribute && parent.getAttribute('data-code')) {
            prefNode = parent;
            break;
          }
          parent = parent.parentNode;
        }
        if (prefNode) break;
      }
      if (prefNode && wrapper.contains(prefNode)) {
        var code = prefNode.getAttribute('data-code');
        console.log('[KNT Map] prefecture clicked:', code);
        selectPref(code);
      }
    });

    // 市区町村チップはwrapperの外（#city-panel内）にあるため、documentでリッスン
    document.addEventListener('click', function (e) {
      var cityTag = e.target.closest('.japan-map-city-tag');
      if (cityTag) {
        selectCity(cityTag.getAttribute('data-city'));
      }
    });
    var tooltip = function () {
      return document.getElementById('map-tooltip');
    };
    wrapper.addEventListener('mouseover', function (e) {
      var tip = tooltip();
      if (!tip) return;
      var prefNode = findPrefNodeAt(e.clientX, e.clientY, wrapper);
      if (!prefNode) { tip.classList.remove('is-visible'); return; }
      var code = normalizeCode(prefNode.getAttribute('data-code'));
      tip.textContent = getPrefName(code);
      tip.classList.add('is-visible');
    });
    wrapper.addEventListener('mouseout', function (e) {
      var tip = tooltip();
      if (tip) tip.classList.remove('is-visible');
    });
    wrapper.addEventListener('mousemove', function (e) {
      var tip = tooltip();
      if (!tip || !tip.classList.contains('is-visible')) return;
      var rect = wrapper.getBoundingClientRect();
      tip.style.left = (e.clientX - rect.left) + 'px';
      tip.style.top = (e.clientY - rect.top) + 'px';
    });
    var prefSelect = document.getElementById('pref-select');
    if (prefSelect) {
      prefSelect.addEventListener('change', function () {
        selectPref(this.value || null);
      });
    }
    var citySelect = document.getElementById('city-select');
    if (citySelect) {
      citySelect.addEventListener('change', function () {
        selectCity(this.value || null);
      });
    }
    var btn = document.getElementById('area-search-btn');
    if (btn) {
      btn.addEventListener('click', function (e) {
        if (this.classList.contains('is-disabled')) {
          e.preventDefault();
        }
      });
    }
    state.mediaQuery = window.matchMedia('(max-width: 767px)');
    state.mediaQuery.addEventListener('change', function () {
      // SVGはPHPインライン埋め込みのためレスポンシブ切替は不要
      // （wp_is_mobile でPC/モバイル判定済み）
    });
  }
  function init() {
    var wrapper = document.getElementById('japan-map');
    if (!wrapper) return;

    // 1. インラインJSONを読み込み
    loadMunicipalityData();

    // 2. ドロップダウンを描画
    populatePrefSelect();
    updateCitySelect(null);
    updateCityPanel(null);
    updateButton();

    // 3. インラインSVGを初期化（色付け等）
    initMap();

    // 4. イベントバインド
    bindEvents();
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
