/**
 * municipalities-full.json 生成スクリプト
 *
 * 前提:
 *   e-Stat の「市区町村を探す」画面で、現在時点 / 全都道府県 / CSV UTF-8(BOM無し)
 *   をダウンロードし、`tmp/municipalities.csv` として配置する。
 *
 * 使い方:
 *   node scripts/build-municipalities-full.mjs
 */
import fs from 'node:fs/promises';

const PREF_NAMES = {
  '01':'北海道','02':'青森県','03':'岩手県','04':'宮城県','05':'秋田県','06':'山形県','07':'福島県',
  '08':'茨城県','09':'栃木県','10':'群馬県','11':'埼玉県','12':'千葉県','13':'東京都','14':'神奈川県',
  '15':'新潟県','16':'富山県','17':'石川県','18':'福井県','19':'山梨県','20':'長野県','21':'岐阜県',
  '22':'静岡県','23':'愛知県','24':'三重県','25':'滋賀県','26':'京都府','27':'大阪府','28':'兵庫県',
  '29':'奈良県','30':'和歌山県','31':'鳥取県','32':'島根県','33':'岡山県','34':'広島県','35':'山口県',
  '36':'徳島県','37':'香川県','38':'愛媛県','39':'高知県','40':'福岡県','41':'佐賀県','42':'長崎県',
  '43':'熊本県','44':'大分県','45':'宮崎県','46':'鹿児島県','47':'沖縄県'
};

// 既存の主要市区町村配列（japan-map.js の MAJOR_CITY_DATA から移植）
const MAJOR_CITIES = {
  '01': ['札幌市','函館市','旭川市','小樽市','帯広市','釧路市','北見市','苫小牧市','江別市','千歳市'],
  '02': ['青森市','弘前市','八戸市','十和田市','むつ市','五所川原市'],
  '03': ['盛岡市','一関市','奥州市','花巻市','北上市','宮古市'],
  '04': ['仙台市','石巻市','大崎市','名取市','登米市','気仙沼市'],
  '05': ['秋田市','横手市','大仙市','由利本荘市','能代市','大館市'],
  '06': ['山形市','鶴岡市','酒田市','米沢市','天童市','東根市'],
  '07': ['福島市','郡山市','いわき市','会津若松市','須賀川市','白河市'],
  '08': ['水戸市','つくば市','日立市','ひたちなか市','古河市','土浦市'],
  '09': ['宇都宮市','小山市','栃木市','足利市','佐野市','那須塩原市'],
  '10': ['前橋市','高崎市','太田市','伊勢崎市','桐生市','館林市'],
  '11': ['さいたま市','川越市','川口市','所沢市','越谷市','草加市','春日部市','上尾市','熊谷市','新座市'],
  '12': ['千葉市','船橋市','松戸市','市川市','柏市','市原市','八千代市','流山市','浦安市','習志野市'],
  '13': ['千代田区','中央区','港区','新宿区','文京区','台東区','墨田区','江東区','品川区','目黒区','大田区','世田谷区','渋谷区','中野区','杉並区','豊島区','北区','荒川区','板橋区','練馬区','足立区','葛飾区','江戸川区','八王子市','立川市','武蔵野市','三鷹市','町田市'],
  '14': ['横浜市','川崎市','相模原市','藤沢市','横須賀市','平塚市','茅ヶ崎市','大和市','厚木市','小田原市','鎌倉市'],
  '15': ['新潟市','長岡市','上越市','三条市','柏崎市','燕市'],
  '16': ['富山市','高岡市','射水市','南砺市','氷見市','砺波市'],
  '17': ['金沢市','白山市','小松市','加賀市','七尾市','野々市市'],
  '18': ['福井市','坂井市','越前市','敦賀市','鯖江市','大野市'],
  '19': ['甲府市','甲斐市','南アルプス市','笛吹市','富士吉田市','北杜市'],
  '20': ['長野市','松本市','上田市','飯田市','佐久市','安曇野市'],
  '21': ['岐阜市','大垣市','各務原市','多治見市','高山市','可児市'],
  '22': ['静岡市','浜松市','富士市','沼津市','磐田市','藤枝市','焼津市','三島市'],
  '23': ['名古屋市','豊田市','岡崎市','一宮市','豊橋市','春日井市','安城市','豊川市','刈谷市','小牧市'],
  '24': ['津市','四日市市','鈴鹿市','松阪市','伊勢市','桑名市'],
  '25': ['大津市','草津市','長浜市','東近江市','彦根市','甲賀市'],
  '26': ['京都市','宇治市','亀岡市','舞鶴市','城陽市','長岡京市','福知山市'],
  '27': ['大阪市','堺市','東大阪市','豊中市','枚方市','吹田市','高槻市','茨木市','八尾市','寝屋川市','岸和田市'],
  '28': ['神戸市','姫路市','尼崎市','西宮市','明石市','加古川市','宝塚市','伊丹市','川西市','芦屋市'],
  '29': ['奈良市','橿原市','生駒市','大和郡山市','天理市','香芝市'],
  '30': ['和歌山市','田辺市','橋本市','紀の川市','岩出市','海南市'],
  '31': ['鳥取市','米子市','倉吉市','境港市','岩美町'],
  '32': ['松江市','出雲市','浜田市','益田市','大田市','安来市'],
  '33': ['岡山市','倉敷市','津山市','総社市','笠岡市','玉野市'],
  '34': ['広島市','福山市','呉市','東広島市','尾道市','三原市','廿日市市'],
  '35': ['下関市','山口市','宇部市','周南市','岩国市','防府市'],
  '36': ['徳島市','阿南市','鳴門市','吉野川市','小松島市','美馬市'],
  '37': ['高松市','丸亀市','三豊市','観音寺市','坂出市','さぬき市'],
  '38': ['松山市','今治市','新居浜市','西条市','四国中央市','宇和島市'],
  '39': ['高知市','南国市','四万十市','須崎市','土佐市','香南市'],
  '40': ['福岡市','北九州市','久留米市','飯塚市','大牟田市','春日市','筑紫野市'],
  '41': ['佐賀市','唐津市','鳥栖市','伊万里市','武雄市','小城市'],
  '42': ['長崎市','佐世保市','諫早市','大村市','島原市','五島市'],
  '43': ['熊本市','八代市','天草市','玉名市','宇城市','合志市'],
  '44': ['大分市','別府市','中津市','佐伯市','日田市','宇佐市'],
  '45': ['宮崎市','都城市','延岡市','日向市','日南市','小林市'],
  '46': ['鹿児島市','霧島市','鹿屋市','薩摩川内市','姶良市','奄美市'],
  '47': ['那覇市','沖縄市','うるま市','浦添市','宜野湾市','名護市','豊見城市','糸満市']
};

function parseCsv(text) {
  const rows = [];
  let row = [];
  let field = '';
  let i = 0;
  let inQuotes = false;

  while (i < text.length) {
    const char = text[i];
    const next = text[i + 1];

    if (char === '"') {
      if (inQuotes && next === '"') {
        field += '"';
        i += 2;
        continue;
      }
      inQuotes = !inQuotes;
      i += 1;
      continue;
    }
    if (!inQuotes && char === ',') {
      row.push(field);
      field = '';
      i += 1;
      continue;
    }
    if (!inQuotes && (char === '\n' || char === '\r')) {
      if (char === '\r' && next === '\n') i += 1;
      row.push(field);
      rows.push(row);
      row = [];
      field = '';
      i += 1;
      continue;
    }
    field += char;
    i += 1;
  }

  if (field.length || row.length) {
    row.push(field);
    rows.push(row);
  }

  const header = rows.shift().map(v => v.replace(/^\uFEFF/, '').trim());
  return rows
    .filter(r => r.some(Boolean))
    .map(r => Object.fromEntries(header.map((key, idx) => [key, (r[idx] || '').trim()])));
}

function toMunicipalityLabel(row) {
  const parent = (row['政令市・郡・支庁・振興局等'] || '').trim();
  const child = (row['市区町村'] || '').trim();

  // 政令指定都市の区: 札幌市中央区 の形式に正規化
  if (parent && child && /市$/.test(parent) && /区$/.test(child)) {
    return parent + child;
  }

  // 郡は単体では入れない。町村だけ使う
  if (/郡$/.test(parent) && child) {
    return child;
  }

  // 市区町村があればそれを採用
  if (child) {
    return child;
  }

  // 市だけの行はそのまま使う
  if (parent && /市$/.test(parent)) {
    return parent;
  }

  return '';
}

async function main() {
  const csvText = await fs.readFile('tmp/municipalities.csv', 'utf8');
  const rows = parseCsv(csvText);

  const result = {};
  Object.keys(PREF_NAMES).forEach(code => {
    result[code] = {
      name: PREF_NAMES[code],
      majorCities: MAJOR_CITIES[code] || [],
      municipalities: []
    };
  });

  for (const row of rows) {
    const regionCode = (row['標準地域コード'] || '').trim();
    if (!/^\d{5}$/.test(regionCode)) continue;

    const prefCode = regionCode.slice(0, 2);
    const label = toMunicipalityLabel(row);
    if (!label) continue;
    if (!result[prefCode]) continue;

    if (!result[prefCode].municipalities.includes(label)) {
      result[prefCode].municipalities.push(label);
    }
  }

  // Sort municipalities
  for (const code of Object.keys(result)) {
    result[code].municipalities.sort((a, b) => a.localeCompare(b, 'ja'));
  }

  await fs.mkdir('data', { recursive: true });
  await fs.writeFile(
    'data/municipalities-full.json',
    JSON.stringify(result, null, 2),
    'utf8'
  );

  // Summary
  let total = 0;
  for (const code of Object.keys(result)) {
    total += result[code].municipalities.length;
  }
  console.log(`data/municipalities-full.json generated (${total} municipalities)`);
}

main().catch(err => {
  console.error(err);
  process.exit(1);
});
