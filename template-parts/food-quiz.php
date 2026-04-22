<?php
/**
 * Template Part: 食べたいもの診断
 *
 * @package KNT_Media
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>
<section class="food-quiz fadeup" id="food-quiz">
    <div class="food-quiz__inner">
        <h2 class="food-quiz__heading">食べたいもの診断</h2>
        <p class="food-quiz__sub">4つの質問に答えるだけで、あなたにぴったりのお店が見つかります</p>

        <div class="food-quiz__steps" id="quiz-steps">
            <!-- Step 1 -->
            <div class="food-quiz__step is-active" data-step="1">
                <p class="food-quiz__question"><span class="food-quiz__step-num">Q1</span>今の気分は？</p>
                <div class="food-quiz__options">
                    <button type="button" class="food-quiz__option" data-key="mood" data-value="gatturi">
                        <span class="food-quiz__option-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><circle cx="9" cy="9" r="1" fill="currentColor"/><circle cx="15" cy="9" r="1" fill="currentColor"/></svg></span>
                        がっつり食べたい
                    </button>
                    <button type="button" class="food-quiz__option" data-key="mood" data-value="assari">
                        <span class="food-quiz__option-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22c5.52 0 10-4.48 10-10S17.52 2 12 2 2 6.48 2 12s4.48 10 10 10z"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/></svg></span>
                        あっさりしたい
                    </button>
                    <button type="button" class="food-quiz__option" data-key="mood" data-value="nomitai">
                        <span class="food-quiz__option-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 2l1.5 9H12l1.5-9"/><path d="M6 11h12v2a6 6 0 01-12 0v-2z"/><path d="M12 17v4"/><path d="M8 21h8"/></svg></span>
                        飲みたい気分
                    </button>
                    <button type="button" class="food-quiz__option" data-key="mood" data-value="mattari">
                        <span class="food-quiz__option-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8h1a4 4 0 010 8h-1"/><path d="M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg></span>
                        まったりしたい
                    </button>
                    <button type="button" class="food-quiz__option" data-key="mood" data-value="waiwai">
                        <span class="food-quiz__option-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg></span>
                        ワイワイしたい
                    </button>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="food-quiz__step" data-step="2">
                <p class="food-quiz__question"><span class="food-quiz__step-num">Q2</span>誰と食べる？</p>
                <div class="food-quiz__options">
                    <button type="button" class="food-quiz__option" data-key="who" data-value="solo">
                        <span class="food-quiz__option-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
                        一人で
                    </button>
                    <button type="button" class="food-quiz__option" data-key="who" data-value="date">
                        <span class="food-quiz__option-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg></span>
                        デート・二人で
                    </button>
                    <button type="button" class="food-quiz__option" data-key="who" data-value="family">
                        <span class="food-quiz__option-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
                        家族で
                    </button>
                    <button type="button" class="food-quiz__option" data-key="who" data-value="friends">
                        <span class="food-quiz__option-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg></span>
                        友達・グループで
                    </button>
                    <button type="button" class="food-quiz__option" data-key="who" data-value="business">
                        <span class="food-quiz__option-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg></span>
                        仕事関係
                    </button>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="food-quiz__step" data-step="3">
                <p class="food-quiz__question"><span class="food-quiz__step-num">Q3</span>予算は？（一人あたり）</p>
                <div class="food-quiz__options">
                    <button type="button" class="food-quiz__option" data-key="budget" data-value="low">サクッと 〜1,000円</button>
                    <button type="button" class="food-quiz__option" data-key="budget" data-value="mid">ふつうに 1,000〜3,000円</button>
                    <button type="button" class="food-quiz__option" data-key="budget" data-value="high">ちょっと贅沢 3,000〜5,000円</button>
                    <button type="button" class="food-quiz__option" data-key="budget" data-value="premium">特別な日に 5,000円〜</button>
                </div>
            </div>

            <!-- Step 4 -->
            <div class="food-quiz__step" data-step="4">
                <p class="food-quiz__question"><span class="food-quiz__step-num">Q4</span>こだわりは？</p>
                <div class="food-quiz__options">
                    <button type="button" class="food-quiz__option" data-key="feature" data-value="private">個室がいい</button>
                    <button type="button" class="food-quiz__option" data-key="feature" data-value="late">深夜もやってる</button>
                    <button type="button" class="food-quiz__option" data-key="feature" data-value="drink">飲み放題ほしい</button>
                    <button type="button" class="food-quiz__option" data-key="feature" data-value="photo">映えるお店</button>
                    <button type="button" class="food-quiz__option" data-key="feature" data-value="none">特になし</button>
                </div>
            </div>
        </div>

        <!-- 結果 -->
        <div class="food-quiz__result" id="quiz-result" style="display:none;">
            <p class="food-quiz__result-label">あなたにおすすめは…</p>
            <h3 class="food-quiz__result-genre" id="quiz-result-genre"></h3>
            <p class="food-quiz__result-desc" id="quiz-result-desc"></p>

            <?php if ( function_exists( 'knt_get_prefectures' ) ) :
                $rd_q_regions = knt_get_regions();
                $rd_q_prefs   = knt_get_prefectures();
                $rd_q_by_reg  = array();
                foreach ( $rd_q_regions as $rk => $rl ) $rd_q_by_reg[ $rk ] = array();
                foreach ( $rd_q_prefs as $p ) {
                    if ( isset( $rd_q_by_reg[ $p['region'] ] ) ) $rd_q_by_reg[ $p['region'] ][] = $p;
                }
            ?>
            <div class="food-quiz__area" data-food-quiz-area>
                <p class="food-quiz__area-label">エリアで絞る（任意）</p>
                <div class="food-quiz__area-row">
                    <select class="food-quiz__area-select" id="quiz-area-select" data-quiz-area-select>
                        <option value="">全国から探す</option>
                        <?php foreach ( $rd_q_regions as $rk => $rl ) : if ( empty( $rd_q_by_reg[ $rk ] ) ) continue; ?>
                            <optgroup label="<?php echo esc_attr( $rl ); ?>">
                                <?php foreach ( $rd_q_by_reg[ $rk ] as $p ) : ?>
                                    <option value="<?php echo esc_attr( $p['name'] ); ?>"
                                            data-area-url="<?php echo esc_url( knt_prefecture_url( $p['code'], $p['name'] ) ); ?>">
                                        <?php echo esc_html( $p['name'] ); ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" class="food-quiz__area-use-saved" data-quiz-area-saved hidden>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        保存済みを使う
                    </button>
                </div>
                <p class="food-quiz__area-hint" data-quiz-area-hint>ジャンル × エリアで検索結果を絞り込めます</p>
            </div>
            <?php endif; ?>

            <a href="#" id="quiz-result-link" class="food-quiz__result-btn">おすすめ記事を見る →</a>
            <button type="button" id="quiz-retry" class="food-quiz__retry">もう一度診断する</button>
        </div>

        <!-- 進捗バー -->
        <div class="food-quiz__progress">
            <div class="food-quiz__progress-bar" id="quiz-progress" style="width:0%"></div>
        </div>
    </div>
</section>
