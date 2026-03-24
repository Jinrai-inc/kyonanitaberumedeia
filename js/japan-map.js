/**
 * KNT Media - Interactive Japan Map
 * 47都道府県のSVGマップ + ドロップダウンセレクター
 */
(function () {
  'use strict';

  /* ========================================
     Prefecture Data (47都道府県 + 主要市区町村)
     ======================================== */
  var JAPAN_DATA = {
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

  /* ========================================
     SVG Path Data (simplified prefecture outlines)
     viewBox = "0 0 560 760"
     ======================================== */
  var PREF_PATHS = {
    '01': 'M310,10 L380,15 410,40 430,80 420,120 390,135 370,115 340,130 300,110 280,80 270,50 285,25Z',
    '02': 'M340,145 L370,140 385,155 380,180 355,190 330,180 325,160Z',
    '03': 'M345,190 L380,185 395,200 385,230 360,240 335,225 330,200Z',
    '04': 'M345,240 L375,235 385,250 375,270 350,275 335,260 340,245Z',
    '05': 'M310,170 L335,165 340,185 330,205 310,210 295,195 300,175Z',
    '06': 'M310,215 L335,210 340,230 330,250 310,255 295,240 300,220Z',
    '07': 'M315,260 L350,255 365,270 355,295 330,300 310,290 305,270Z',
    '08': 'M320,340 L345,335 360,350 355,370 335,380 315,370 310,350Z',
    '09': 'M300,320 L330,315 340,330 330,350 310,355 295,340 298,325Z',
    '10': 'M275,315 L300,310 310,325 300,345 280,350 268,335 270,320Z',
    '11': 'M285,350 L315,345 325,360 315,375 295,380 280,370 282,355Z',
    '12': 'M325,370 L355,365 370,380 365,400 340,405 320,395 318,378Z',
    '13': 'M295,380 L320,375 330,390 325,405 305,410 290,400 288,385Z',
    '14': 'M290,410 L320,405 335,415 330,435 305,440 285,430 283,415Z',
    '15': 'M255,245 L280,235 295,250 290,280 275,300 255,295 248,270Z',
    '16': 'M230,290 L255,285 265,300 258,315 240,318 228,305Z',
    '17': 'M215,305 L240,298 250,315 245,335 225,340 210,325Z',
    '18': 'M220,340 L242,335 252,350 245,370 228,372 215,358Z',
    '19': 'M265,380 L285,375 295,390 288,405 270,408 260,395Z',
    '20': 'M248,330 L272,325 280,345 275,370 258,378 240,365 242,340Z',
    '21': 'M230,370 L255,365 265,380 258,400 238,405 225,392Z',
    '22': 'M255,410 L285,405 300,418 295,440 270,448 250,435 248,418Z',
    '23': 'M225,405 L255,400 268,415 260,435 240,442 220,430 218,412Z',
    '24': 'M210,435 L238,430 248,445 242,465 222,470 208,455Z',
    '25': 'M195,400 L218,395 228,410 222,428 205,432 190,418Z',
    '26': 'M180,380 L205,375 215,392 210,412 192,418 175,405Z',
    '27': 'M185,425 L210,420 222,435 218,455 200,462 182,448Z',
    '28': 'M155,405 L182,398 195,415 190,438 170,445 148,432Z',
    '29': 'M195,445 L218,440 228,455 222,472 205,478 190,465Z',
    '30': 'M175,465 L200,458 212,475 205,495 185,500 168,488Z',
    '31': 'M148,395 L168,390 175,402 170,415 155,418 145,408Z',
    '32': 'M120,400 L148,395 158,410 152,428 132,432 115,420Z',
    '33': 'M135,435 L160,428 172,442 168,460 148,465 130,455Z',
    '34': 'M100,430 L130,425 142,440 138,460 118,468 95,455Z',
    '35': 'M75,445 L100,440 112,455 105,475 85,480 70,465Z',
    '36': 'M165,480 L185,475 195,490 188,505 170,508 160,498Z',
    '37': 'M150,470 L170,465 178,478 172,492 155,495 145,485Z',
    '38': 'M130,480 L155,475 165,492 158,510 138,515 125,500Z',
    '39': 'M145,510 L170,505 182,518 175,540 155,545 140,530Z',
    '40': 'M75,490 L105,485 118,500 112,520 90,525 72,510Z',
    '41': 'M58,505 L80,500 90,512 85,528 68,532 55,520Z',
    '42': 'M35,510 L60,505 72,520 65,540 45,545 30,530Z',
    '43': 'M65,530 L92,525 105,540 98,565 78,570 60,555Z',
    '44': 'M95,510 L120,505 130,518 125,535 105,540 90,528Z',
    '45': 'M82,570 L105,565 115,580 110,605 90,610 78,595Z',
    '46': 'M68,610 L95,605 108,620 100,650 80,655 62,640Z',
    '47': 'M25,660 L55,655 65,670 60,690 40,695 20,680Z'
  };

  /* ========================================
     State
     ======================================== */
  var state = {
    selectedPref: null,
    selectedCity: null
  };

  /* ========================================
     Init
     ======================================== */
  function init() {
    var mapWrapper = document.getElementById('japan-map');
    if (!mapWrapper) return;

    renderSVG(mapWrapper);
    populatePrefSelect();
    bindEvents();
  }

  /* ========================================
     Render SVG Map
     ======================================== */
  function renderSVG(container) {
    var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.setAttribute('viewBox', '0 0 560 760');
    svg.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
    svg.setAttribute('role', 'img');
    svg.setAttribute('aria-label', '日本地図');

    // Draw prefecture paths
    Object.keys(PREF_PATHS).forEach(function (code) {
      var path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
      path.setAttribute('d', PREF_PATHS[code]);
      path.setAttribute('class', 'pref-path');
      path.setAttribute('data-code', code);
      path.setAttribute('data-name', JAPAN_DATA[code].name);
      svg.appendChild(path);
    });

    // Region labels
    var regions = [
      { text: '北海道', x: 370, y: 75 },
      { text: '東北', x: 360, y: 220 },
      { text: '関東', x: 340, y: 370 },
      { text: '中部', x: 260, y: 340 },
      { text: '近畿', x: 190, y: 440 },
      { text: '中国', x: 120, y: 448 },
      { text: '四国', x: 160, y: 510 },
      { text: '九州', x: 70, y: 550 },
      { text: '沖縄', x: 40, y: 680 }
    ];

    regions.forEach(function (r) {
      var text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
      text.setAttribute('x', r.x);
      text.setAttribute('y', r.y);
      text.setAttribute('class', 'region-label');
      text.setAttribute('text-anchor', 'middle');
      text.textContent = r.text;
      svg.appendChild(text);
    });

    container.appendChild(svg);

    // Tooltip element
    var tooltip = document.createElement('div');
    tooltip.className = 'japan-map-tooltip';
    tooltip.id = 'map-tooltip';
    container.appendChild(tooltip);
  }

  /* ========================================
     Populate Prefecture Select
     ======================================== */
  function populatePrefSelect() {
    var select = document.getElementById('pref-select');
    if (!select) return;

    Object.keys(JAPAN_DATA).forEach(function (code) {
      var option = document.createElement('option');
      option.value = code;
      option.textContent = JAPAN_DATA[code].name;
      select.appendChild(option);
    });
  }

  /* ========================================
     Update City Select
     ======================================== */
  function updateCitySelect(prefCode) {
    var select = document.getElementById('city-select');
    if (!select) return;

    // Clear existing options
    select.innerHTML = '';

    if (!prefCode || !JAPAN_DATA[prefCode]) {
      select.innerHTML = '<option value="">先に都道府県を選択してください</option>';
      select.disabled = true;
      return;
    }

    select.disabled = false;
    var defaultOpt = document.createElement('option');
    defaultOpt.value = '';
    defaultOpt.textContent = '市区町村を選択';
    select.appendChild(defaultOpt);

    JAPAN_DATA[prefCode].cities.forEach(function (city) {
      var option = document.createElement('option');
      option.value = city;
      option.textContent = city;
      select.appendChild(option);
    });
  }

  /* ========================================
     Update City Panel (tag buttons)
     ======================================== */
  function updateCityPanel(prefCode) {
    var panel = document.getElementById('city-panel');
    if (!panel) return;

    if (!prefCode || !JAPAN_DATA[prefCode]) {
      panel.innerHTML = '';
      panel.classList.remove('has-cities');
      return;
    }

    var data = JAPAN_DATA[prefCode];
    var html = '<p class="japan-map-city-panel-title">' + data.name + ' の主要エリア</p>';
    html += '<div class="japan-map-city-tags">';
    data.cities.forEach(function (city) {
      html += '<button type="button" class="japan-map-city-tag" data-city="' + city + '">' + city + '</button>';
    });
    html += '</div>';

    panel.innerHTML = html;
    panel.classList.add('has-cities');
  }

  /* ========================================
     Update Search Button
     ======================================== */
  function updateButton() {
    var btn = document.getElementById('area-search-btn');
    if (!btn) return;

    if (!state.selectedPref) {
      btn.classList.add('is-disabled');
      btn.href = '#';
      return;
    }

    btn.classList.remove('is-disabled');

    var section = document.querySelector('.japan-map-section');
    var urlPattern = section ? section.getAttribute('data-area-url') : 'search';
    var prefName = JAPAN_DATA[state.selectedPref].name;
    var query = state.selectedCity || prefName;

    if (urlPattern === 'taxonomy') {
      var base = window.location.origin;
      var slug = prefName.replace(/[都府県]/g, '').replace('北海道', '北海道');
      btn.href = base + '/area/' + encodeURIComponent(slug) + '/';
    } else {
      btn.href = '/?s=' + encodeURIComponent(query + ' グルメ');
    }
  }

  /* ========================================
     Highlight map prefecture
     ======================================== */
  function highlightPref(code) {
    // Remove all active
    document.querySelectorAll('.pref-path.is-active').forEach(function (el) {
      el.classList.remove('is-active');
    });

    if (code) {
      var path = document.querySelector('.pref-path[data-code="' + code + '"]');
      if (path) {
        path.classList.add('is-active');
      }
    }
  }

  /* ========================================
     Select a prefecture (from any source)
     ======================================== */
  function selectPref(code) {
    state.selectedPref = code;
    state.selectedCity = null;

    highlightPref(code);
    updateCitySelect(code);
    updateCityPanel(code);
    updateButton();

    // Sync dropdown
    var select = document.getElementById('pref-select');
    if (select && select.value !== code) {
      select.value = code || '';
    }

    // Reset city dropdown
    var citySelect = document.getElementById('city-select');
    if (citySelect) {
      citySelect.value = '';
    }
  }

  /* ========================================
     Select a city
     ======================================== */
  function selectCity(city) {
    state.selectedCity = city;

    // Highlight city tag
    document.querySelectorAll('.japan-map-city-tag').forEach(function (el) {
      el.classList.toggle('is-selected', el.getAttribute('data-city') === city);
    });

    // Sync dropdown
    var citySelect = document.getElementById('city-select');
    if (citySelect && citySelect.value !== city) {
      citySelect.value = city || '';
    }

    updateButton();
  }

  /* ========================================
     Bind Events
     ======================================== */
  function bindEvents() {
    // Map path clicks
    document.addEventListener('click', function (e) {
      var path = e.target.closest('.pref-path');
      if (path) {
        selectPref(path.getAttribute('data-code'));
        return;
      }

      // City tag clicks
      var cityTag = e.target.closest('.japan-map-city-tag');
      if (cityTag) {
        selectCity(cityTag.getAttribute('data-city'));
        return;
      }
    });

    // Map hover tooltip
    var tooltip = document.getElementById('map-tooltip');
    if (tooltip) {
      document.addEventListener('mouseover', function (e) {
        var path = e.target.closest('.pref-path');
        if (path) {
          tooltip.textContent = path.getAttribute('data-name');
          tooltip.classList.add('is-visible');
        }
      });

      document.addEventListener('mouseout', function (e) {
        var path = e.target.closest('.pref-path');
        if (path) {
          tooltip.classList.remove('is-visible');
        }
      });

      document.addEventListener('mousemove', function (e) {
        if (tooltip.classList.contains('is-visible')) {
          var wrapper = document.querySelector('.japan-map-svg-wrapper');
          if (wrapper) {
            var rect = wrapper.getBoundingClientRect();
            tooltip.style.left = (e.clientX - rect.left) + 'px';
            tooltip.style.top = (e.clientY - rect.top) + 'px';
          }
        }
      });
    }

    // Prefecture dropdown
    var prefSelect = document.getElementById('pref-select');
    if (prefSelect) {
      prefSelect.addEventListener('change', function () {
        selectPref(this.value || null);
      });
    }

    // City dropdown
    var citySelect = document.getElementById('city-select');
    if (citySelect) {
      citySelect.addEventListener('change', function () {
        selectCity(this.value || null);
      });
    }

    // Search button
    var btn = document.getElementById('area-search-btn');
    if (btn) {
      btn.addEventListener('click', function (e) {
        if (this.classList.contains('is-disabled')) {
          e.preventDefault();
        }
      });
    }
  }

  /* ========================================
     DOM Ready
     ======================================== */
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
