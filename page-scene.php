<?php
/**
 * Template Name: シーン別お店探し
 *
 * /scene/{scene-slug}/ でアクセスされるページ。
 * 都道府県×市区町村を選んでシーン別記事に遷移。
 *
 * @package KNT_Media
 */

get_header();

// URLからシーンキーを取得
$scene_slug = get_query_var( 'knt_scene_slug', '' );
$scene_key  = '';
$scene      = null;

foreach ( KNT_SCENES as $key => $s ) {
    if ( $s['slug'] === $scene_slug ) {
        $scene_key = $key;
        $scene     = $s;
        break;
    }
}

if ( ! $scene ) {
    // フォールバック：全シーン一覧を表示
    ?>
    <div class="container" style="padding: 48px 0;">
        <h1 class="section__title" style="text-align: center; margin-bottom: 32px;">シーンからお店を探す</h1>
        <div class="grid grid--3">
            <?php foreach ( KNT_SCENES as $k => $s ) : ?>
                <a href="<?php echo esc_url( home_url( '/scene/' . $s['slug'] . '/' ) ); ?>" class="scene-card fadeup">
                    <h3 class="scene-card__title"><?php echo esc_html( $s['label'] ); ?></h3>
                    <p class="scene-card__desc"><?php echo esc_html( $s['description'] ); ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
} else {
    // 特定シーンのページ
    ?>
    <div class="container" style="padding: 48px 0;">
        <?php knt_breadcrumb(); ?>

        <header style="text-align: center; margin-bottom: 40px;">
            <p style="color: var(--color-accent); font-weight: 700; font-size: 0.85rem; margin-bottom: 8px;">SCENE</p>
            <h1 class="section__title"><?php echo esc_html( $scene['label'] ); ?>におすすめのお店</h1>
            <p style="color: var(--color-text-sub); margin-top: 8px; max-width: 600px; margin-left: auto; margin-right: auto;">
                <?php echo esc_html( $scene['description'] ); ?>
            </p>
        </header>

        <div class="scene-search fadeup">
            <div class="scene-search__card">
                <h2 class="scene-search__title">エリアを選んで<?php echo esc_html( $scene['label'] ); ?>向きのお店を探す</h2>

                <div class="scene-search__form">
                    <div class="scene-search__field">
                        <label for="scene-pref">都道府県</label>
                        <select id="scene-pref" class="japan-map-dropdown">
                            <option value="">都道府県を選択</option>
                        </select>
                    </div>
                    <div class="scene-search__field">
                        <label for="scene-city">市区町村</label>
                        <select id="scene-city" class="japan-map-dropdown" disabled>
                            <option value="">まず都道府県を選択</option>
                        </select>
                    </div>
                    <a href="#" id="scene-search-btn" class="btn btn--primary" style="align-self: flex-end; pointer-events: none; opacity: 0.5;">
                        この条件で記事を探す →
                    </a>
                </div>
            </div>
        </div>

        <?php
        // このシーンに関連する記事を表示
        $scene_posts = new WP_Query( array(
            'posts_per_page' => 9,
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'meta_key'       => '_knt_scene',
            'meta_value'     => $scene_key,
        ) );

        if ( $scene_posts->have_posts() ) :
        ?>
        <section class="section" style="margin-top: 48px;">
            <h2 class="section__title"><?php echo esc_html( $scene['label'] ); ?>の人気記事</h2>
            <div class="grid grid--3" style="margin-top: 24px;">
                <?php while ( $scene_posts->have_posts() ) : $scene_posts->the_post(); ?>
                    <article class="card fadeup">
                        <?php get_template_part( 'template-parts/card' ); ?>
                    </article>
                <?php endwhile; ?>
            </div>
        </section>
        <?php
            wp_reset_postdata();
        endif;
        ?>
    </div>

    <script>
    (function() {
        var SCENE_SLUG = '<?php echo esc_js( $scene['slug'] ); ?>';
        var DATA = typeof kntMapData !== 'undefined' && kntMapData.municipalities
            ? kntMapData.municipalities
            : (typeof MAJOR_CITY_DATA !== 'undefined' ? MAJOR_CITY_DATA : null);

        // municipalities-full.json をインラインで渡す
        <?php
        $json_path = KNT_DIR . '/data/municipalities-full.json';
        if ( file_exists( $json_path ) ) {
            $raw = file_get_contents( $json_path );
            echo 'if (!DATA) { DATA = ' . $raw . '; }';
        }
        ?>

        var prefSelect = document.getElementById('scene-pref');
        var citySelect = document.getElementById('scene-city');
        var searchBtn  = document.getElementById('scene-search-btn');

        if (DATA && prefSelect) {
            Object.keys(DATA).sort().forEach(function(code) {
                var opt = document.createElement('option');
                opt.value = code;
                opt.textContent = DATA[code].name;
                prefSelect.appendChild(opt);
            });
        }

        prefSelect.addEventListener('change', function() {
            var code = this.value;
            citySelect.innerHTML = '';
            if (!code || !DATA[code]) {
                citySelect.innerHTML = '<option value="">まず都道府県を選択</option>';
                citySelect.disabled = true;
                searchBtn.style.pointerEvents = 'none';
                searchBtn.style.opacity = '0.5';
                searchBtn.href = '#';
                return;
            }
            citySelect.disabled = false;
            var defOpt = document.createElement('option');
            defOpt.value = '';
            defOpt.textContent = '全域';
            citySelect.appendChild(defOpt);

            var cities = DATA[code].municipalities || DATA[code].cities || [];
            cities.forEach(function(c) {
                var opt = document.createElement('option');
                opt.value = c;
                opt.textContent = c;
                citySelect.appendChild(opt);
            });

            updateSearchUrl();
        });

        citySelect.addEventListener('change', updateSearchUrl);

        function updateSearchUrl() {
            var prefCode = prefSelect.value;
            if (!prefCode) return;
            var area = citySelect.value || DATA[prefCode].name;
            var q = encodeURIComponent(area + ' ' + SCENE_SLUG);
            searchBtn.href = '/?s=' + q;
            searchBtn.style.pointerEvents = 'auto';
            searchBtn.style.opacity = '1';
        }
    })();
    </script>
    <?php
}

get_footer();
