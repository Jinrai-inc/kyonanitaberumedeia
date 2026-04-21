/**
 * Redesign JS — prefecture picker / area gate / archive tweaks / header search
 *
 * すべての機能は既存の main.js / japan-map.js と独立して動作する。
 * 対象 DOM が存在しない時は no-op。
 */
(function () {
    'use strict';

    var LS_AREA    = 'knt-area';
    var LS_TWEAKS  = 'knt-tweaks';
    // セッション内での「閉じた」フラグ（タブを閉じるとクリア）
    var SS_GATE_D  = 'knt-area-gate-dismissed-session';

    function $(sel, root) { return (root || document).querySelector(sel); }
    function $$(sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); }

    function safeParse(json) {
        try { return JSON.parse(json); } catch (e) { return null; }
    }
    function lsGet(key) {
        try { return localStorage.getItem(key); } catch (e) { return null; }
    }
    function lsSet(key, val) {
        try { localStorage.setItem(key, val); } catch (e) { /* ignore */ }
    }
    function ssGet(key) {
        try { return sessionStorage.getItem(key); } catch (e) { return null; }
    }
    function ssSet(key, val) {
        try { sessionStorage.setItem(key, val); } catch (e) { /* ignore */ }
    }

    /* ==================================================================
       1. Prefecture Picker — 地方タブ切替
       ================================================================== */
    function initPrefPicker() {
        var pickers = $$('[data-rd-pref-picker]');
        if (!pickers.length) return;

        pickers.forEach(function (picker) {
            var tabs   = $$('.rd-pref-picker__tab', picker);
            var panels = $$('.rd-pref-picker__panel', picker);

            tabs.forEach(function (tab) {
                tab.addEventListener('click', function () {
                    var region = tab.getAttribute('data-region');
                    tabs.forEach(function (t) {
                        var active = t === tab;
                        t.classList.toggle('is-active', active);
                        t.setAttribute('aria-selected', active ? 'true' : 'false');
                    });
                    panels.forEach(function (p) {
                        var active = p.getAttribute('data-region-panel') === region;
                        p.classList.toggle('is-active', active);
                        p.setAttribute('aria-hidden', active ? 'false' : 'true');
                    });
                });
            });
        });
    }

    /* ==================================================================
       2. Area Gate — いつでも呼び出せる都道府県選択モーダル
          - 初回訪問 (保存無し かつ セッション内で閉じていない) → 自動表示
          - 以降: [data-rd-area-gate-open] クリック or 保存済みの再訪問時は閉じたまま
          - 選択済の場合、ヘッダーバッジに都道府県名を表示
          - 開いた際は保存済エリアの地方タブを自動選択
       ================================================================== */
    function initAreaGate() {
        var gate = $('[data-rd-area-gate]');

        // バッジは常に更新（gate が無くても動くよう先に処理）
        syncAreaBadge();

        if (!gate) return;

        var saved = safeParse(lsGet(LS_AREA));

        // 初回訪問(保存無し) & 同セッション内で閉じていない → 自動表示
        if (!saved && ssGet(SS_GATE_D) !== '1') {
            openGate();
        }

        // タブ切替
        $$('.rd-area-gate__tab', gate).forEach(function (tab) {
            tab.addEventListener('click', function () {
                activateRegion(tab.getAttribute('data-region'));
            });
        });

        // 都道府県選択
        $$('.rd-area-gate__pref', gate).forEach(function (btn) {
            btn.addEventListener('click', function () {
                var code   = btn.getAttribute('data-pref-code');
                var name   = btn.getAttribute('data-pref-name');
                var region = btn.getAttribute('data-pref-region');
                var url    = btn.getAttribute('data-pref-url');

                lsSet(LS_AREA, JSON.stringify({ code: code, name: name, region: region }));
                syncAreaBadge();
                closeGate();

                if (url && url !== '#') {
                    setTimeout(function () { window.location.href = url; }, 120);
                }
            });
        });

        // 閉じる / スキップ — セッション内だけの一時フラグ
        $$('[data-rd-area-gate-close]', gate).forEach(function (el) {
            el.addEventListener('click', closeGate);
        });
        var skip = $('[data-rd-area-gate-skip]', gate);
        if (skip) skip.addEventListener('click', closeGate);

        // ESC キーで閉じる
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !gate.hidden) closeGate();
        });

        // 「エリア変更」トリガー — ページ内どこからでも呼び出せる
        document.addEventListener('click', function (e) {
            var trigger = e.target.closest && e.target.closest('[data-rd-area-gate-open]');
            if (trigger) {
                e.preventDefault();
                openGate();
            }
        });

        function activateRegion(region) {
            $$('.rd-area-gate__tab', gate).forEach(function (t) {
                var active = t.getAttribute('data-region') === region;
                t.classList.toggle('is-active', active);
                t.setAttribute('aria-selected', active ? 'true' : 'false');
            });
            $$('.rd-area-gate__panel', gate).forEach(function (p) {
                var active = p.getAttribute('data-region-panel') === region;
                p.classList.toggle('is-active', active);
                p.setAttribute('aria-hidden', active ? 'false' : 'true');
            });
        }

        function highlightSavedPref() {
            var s = safeParse(lsGet(LS_AREA));
            $$('.rd-area-gate__pref', gate).forEach(function (b) {
                b.classList.toggle('is-current', s && b.getAttribute('data-pref-code') === s.code);
            });
        }

        function openGate() {
            var s = safeParse(lsGet(LS_AREA));
            if (s && s.region) activateRegion(s.region);
            highlightSavedPref();
            gate.hidden = false;
            gate.setAttribute('aria-hidden', 'false');
            document.body.classList.add('rd-no-scroll');
            requestAnimationFrame(function () { gate.classList.add('is-visible'); });
        }
        function closeGate() {
            ssSet(SS_GATE_D, '1');
            gate.classList.remove('is-visible');
            document.body.classList.remove('rd-no-scroll');
            setTimeout(function () {
                gate.hidden = true;
                gate.setAttribute('aria-hidden', 'true');
            }, 240);
        }
    }

    /* バッジのラベルを現在保存されているエリア名に同期 */
    function syncAreaBadge() {
        var saved = safeParse(lsGet(LS_AREA));
        $$('[data-rd-area-badge-label]').forEach(function (el) {
            if (saved && saved.name) {
                el.textContent = saved.name;
                var badge = el.closest('.rd-area-badge');
                if (badge) badge.classList.add('rd-area-badge--set');
            } else {
                el.textContent = 'エリアを選ぶ';
                var badge2 = el.closest('.rd-area-badge');
                if (badge2) badge2.classList.remove('rd-area-badge--set');
            }
        });
    }

    /* ==================================================================
       3. Archive Tweaks — カード密度 & レイアウト切替
       ================================================================== */
    function initArchiveTweaks() {
        var tweaks = $('[data-rd-tweaks]');
        if (!tweaks) return;

        // アーカイブ系ページでのみ出す（.grid--3 が存在するページ）
        if (!$('.grid--3')) return;

        tweaks.hidden = false;

        var panel = $('[data-rd-tweaks-panel]', tweaks);
        var saved = safeParse(lsGet(LS_TWEAKS)) || { density: 3, layout: 'A' };

        apply(saved);
        sync(saved);

        $$('[data-rd-tweaks-toggle]', tweaks).forEach(function (btn) {
            btn.addEventListener('click', function () {
                var isOpen = !panel.hidden;
                panel.hidden = isOpen;
                tweaks.classList.toggle('is-open', !isOpen);
            });
        });

        $$('[data-rd-tweaks-density]', tweaks).forEach(function (btn) {
            btn.addEventListener('click', function () {
                saved.density = parseInt(btn.getAttribute('data-rd-tweaks-density'), 10);
                persist();
                apply(saved);
                sync(saved);
            });
        });
        $$('[data-rd-tweaks-layout]', tweaks).forEach(function (btn) {
            btn.addEventListener('click', function () {
                saved.layout = btn.getAttribute('data-rd-tweaks-layout');
                persist();
                apply(saved);
                sync(saved);
            });
        });

        var reset = $('[data-rd-tweaks-reset]', tweaks);
        if (reset) reset.addEventListener('click', function () {
            saved = { density: 3, layout: 'A' };
            persist();
            apply(saved);
            sync(saved);
        });

        function apply(s) {
            document.body.classList.toggle('rd-density-4', s.density === 4);
            document.body.classList.toggle('rd-density-3', s.density !== 4);
            document.body.classList.toggle('rd-layout-b',  s.layout === 'B');
            document.body.classList.toggle('rd-layout-a',  s.layout !== 'B');
        }
        function sync(s) {
            $$('[data-rd-tweaks-density]', tweaks).forEach(function (b) {
                b.classList.toggle('is-active', parseInt(b.getAttribute('data-rd-tweaks-density'), 10) === s.density);
            });
            $$('[data-rd-tweaks-layout]', tweaks).forEach(function (b) {
                b.classList.toggle('is-active', b.getAttribute('data-rd-tweaks-layout') === s.layout);
            });
        }
        function persist() { lsSet(LS_TWEAKS, JSON.stringify(saved)); }
    }

    /* ==================================================================
       4. Header Search — 開閉 + ESC
       ================================================================== */
    function initHeaderSearch() {
        var root = $('[data-rd-header-search]');
        if (!root) return;

        var trigger = $('[data-rd-header-search-open]', root);
        var panel   = $('[data-rd-header-search-panel]', root);
        var input   = $('[data-rd-header-search-input]', root);
        var closes  = $$('[data-rd-header-search-close]', root);

        if (!trigger || !panel || !input) return;

        function open() {
            panel.hidden = false;
            panel.setAttribute('aria-hidden', 'false');
            root.classList.add('is-open');
            requestAnimationFrame(function () { input.focus(); });
        }
        function close() {
            root.classList.remove('is-open');
            panel.setAttribute('aria-hidden', 'true');
            setTimeout(function () { panel.hidden = true; }, 200);
        }

        trigger.addEventListener('click', function (e) {
            e.preventDefault();
            if (panel.hidden) open(); else close();
        });
        closes.forEach(function (b) { b.addEventListener('click', close); });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !panel.hidden) close();
        });
        document.addEventListener('click', function (e) {
            if (!panel.hidden && !root.contains(e.target)) close();
        });
    }

    /* ==================================================================
       Boot
       ================================================================== */
    function ready(fn) {
        if (document.readyState !== 'loading') fn();
        else document.addEventListener('DOMContentLoaded', fn);
    }

    ready(function () {
        initPrefPicker();
        initAreaGate();
        initArchiveTweaks();
        initHeaderSearch();
    });
})();
