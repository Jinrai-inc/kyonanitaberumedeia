<?php
/**
 * Template Part: Archive Tweaks Panel
 *
 * アーカイブページ右下に表示されるフロートパネル。
 * カードのレイアウト(A/B) と 密度(3カラム/4カラム) を切替。
 * localStorage('knt-tweaks') に保存。JS が .grid--3 / archive body にクラスを付与。
 *
 * @package KNT_Media
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="rd-tweaks" data-rd-tweaks hidden>
    <button type="button" class="rd-tweaks__fab" data-rd-tweaks-toggle aria-label="表示設定を開く">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="12" cy="12" r="3"/>
            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
        </svg>
    </button>
    <div class="rd-tweaks__panel" data-rd-tweaks-panel hidden>
        <div class="rd-tweaks__head">
            <h3 class="rd-tweaks__title">表示をカスタマイズ</h3>
            <button type="button" class="rd-tweaks__close" data-rd-tweaks-toggle aria-label="閉じる">&times;</button>
        </div>

        <div class="rd-tweaks__group">
            <span class="rd-tweaks__label">カード密度</span>
            <div class="rd-tweaks__options" role="radiogroup" aria-label="カード密度">
                <button type="button" class="rd-tweaks__opt" data-rd-tweaks-density="3" role="radio">
                    <span class="rd-tweaks__ico">
                        <span></span><span></span><span></span>
                    </span>
                    <span>ゆったり<br><small>3カラム</small></span>
                </button>
                <button type="button" class="rd-tweaks__opt" data-rd-tweaks-density="4" role="radio">
                    <span class="rd-tweaks__ico rd-tweaks__ico--4">
                        <span></span><span></span><span></span><span></span>
                    </span>
                    <span>たくさん<br><small>4カラム</small></span>
                </button>
            </div>
        </div>

        <div class="rd-tweaks__group">
            <span class="rd-tweaks__label">カードレイアウト</span>
            <div class="rd-tweaks__options" role="radiogroup" aria-label="カードレイアウト">
                <button type="button" class="rd-tweaks__opt" data-rd-tweaks-layout="A" role="radio">
                    <span class="rd-tweaks__swatch rd-tweaks__swatch--a"></span>
                    <span>標準<br><small>画像＋下文</small></span>
                </button>
                <button type="button" class="rd-tweaks__opt" data-rd-tweaks-layout="B" role="radio">
                    <span class="rd-tweaks__swatch rd-tweaks__swatch--b"></span>
                    <span>タイトル重ね<br><small>画像にタイトル</small></span>
                </button>
            </div>
        </div>

        <button type="button" class="rd-tweaks__reset" data-rd-tweaks-reset>設定をリセット</button>
    </div>
</div>
