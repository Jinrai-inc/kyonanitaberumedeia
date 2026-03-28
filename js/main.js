/**
 * KNT Media - Main JavaScript (Enhanced)
 */
(function () {
  'use strict';

  // ========================================
  // Mobile Menu Toggle
  // ========================================
  var menuToggle = document.querySelector('.menu-toggle');
  var mobileMenu = document.getElementById('mobile-menu');

  if (menuToggle && mobileMenu) {
    menuToggle.addEventListener('click', function () {
      var isOpen = mobileMenu.classList.toggle('is-open');
      menuToggle.classList.toggle('is-active');
      menuToggle.setAttribute('aria-expanded', isOpen);
      menuToggle.setAttribute('aria-label', isOpen ? 'メニューを閉じる' : 'メニューを開く');
    });
  }

  // ========================================
  // FadeUp on Scroll (Intersection Observer)
  // ========================================
  function initFadeUp() {
    var fadeElements = document.querySelectorAll('.fadeup:not(.is-visible)');
    if (fadeElements.length > 0 && 'IntersectionObserver' in window) {
      var observer = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              entry.target.classList.add('is-visible');
              observer.unobserve(entry.target);
            }
          });
        },
        { threshold: 0.1, rootMargin: '0px 0px -40px 0px' }
      );
      fadeElements.forEach(function (el) { observer.observe(el); });
    } else {
      fadeElements.forEach(function (el) { el.classList.add('is-visible'); });
    }
  }
  initFadeUp();

  // ========================================
  // Stats Counter Animation
  // ========================================
  var counterEls = document.querySelectorAll('.stats-counter__number[data-target]');
  if (counterEls.length > 0 && 'IntersectionObserver' in window) {
    var counterObserver = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          var el = entry.target;
          var target = parseInt(el.getAttribute('data-target'), 10);
          var duration = 1500;
          var start = 0;
          var startTime = null;
          function animate(ts) {
            if (!startTime) startTime = ts;
            var progress = Math.min((ts - startTime) / duration, 1);
            var eased = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.floor(eased * target).toLocaleString();
            if (progress < 1) requestAnimationFrame(animate);
          }
          requestAnimationFrame(animate);
          counterObserver.unobserve(el);
        }
      });
    }, { threshold: 0.3 });
    counterEls.forEach(function(el) { counterObserver.observe(el); });
  }

  // ========================================
  // Table of Contents Toggle
  // ========================================
  var tocToggle = document.getElementById('toc-toggle');
  var tocList = document.getElementById('toc-list');
  if (tocToggle && tocList) {
    tocToggle.addEventListener('click', function () {
      var isHidden = tocList.style.display === 'none';
      tocList.style.display = isHidden ? 'block' : 'none';
    });
  }

  // ========================================
  // Smooth scroll for anchor links
  // ========================================
  document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
    anchor.addEventListener('click', function (e) {
      var targetId = this.getAttribute('href');
      if (targetId === '#') return;
      var target = document.querySelector(targetId);
      if (target) {
        e.preventDefault();
        var headerHeight = document.querySelector('.site-header')
          ? document.querySelector('.site-header').offsetHeight : 0;
        var targetPosition = target.getBoundingClientRect().top + window.pageYOffset - headerHeight - 16;
        window.scrollTo({ top: targetPosition, behavior: 'smooth' });
      }
    });
  });

  // ========================================
  // Header shadow on scroll
  // ========================================
  var header = document.querySelector('.site-header');
  if (header) {
    window.addEventListener('scroll', function () {
      header.style.boxShadow = window.pageYOffset > 100
        ? '0 2px 20px rgba(0,0,0,0.06)' : 'none';
    }, { passive: true });
  }

  // ========================================
  // Sticky App Banner (右下追尾)
  // ========================================
  var stickyBanner = document.getElementById('sticky-app-banner');
  var stickyClose = document.getElementById('sticky-app-banner-close');
  var stickyDismissed = false;

  if (stickyBanner) {
    window.addEventListener('scroll', function () {
      if (!stickyDismissed && window.pageYOffset > 600) {
        stickyBanner.classList.add('is-visible');
      }
    }, { passive: true });

    if (stickyClose) {
      stickyClose.addEventListener('click', function () {
        stickyBanner.classList.remove('is-visible');
        stickyDismissed = true;
      });
    }
  }

  // ========================================
  // Back to Top Button
  // ========================================
  var backToTop = document.getElementById('back-to-top');
  if (backToTop) {
    window.addEventListener('scroll', function () {
      backToTop.classList.toggle('is-visible', window.pageYOffset > 400);
    }, { passive: true });

    backToTop.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  // ========================================
  // Toast Notification
  // ========================================
  var toastEl = document.getElementById('toast');
  var toastTimer = null;

  function showToast(message) {
    if (!toastEl) return;
    toastEl.textContent = message;
    toastEl.classList.add('is-visible');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(function () {
      toastEl.classList.remove('is-visible');
    }, 2000);
  }

  // ========================================
  // Copy Link (Share Button)
  // ========================================
  document.querySelectorAll('.share-buttons__btn--copy').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var url = this.getAttribute('data-url');
      if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(function () {
          btn.classList.add('is-copied');
          showToast('リンクをコピーしました');
          setTimeout(function () { btn.classList.remove('is-copied'); }, 2000);
        });
      }
    });
  });

  // ========================================
  // Bookmark (localStorage)
  // ========================================
  var BOOKMARK_KEY = 'knt_bookmarks';

  function getBookmarks() {
    try {
      return JSON.parse(localStorage.getItem(BOOKMARK_KEY)) || [];
    } catch (e) {
      return [];
    }
  }

  function saveBookmarks(bookmarks) {
    localStorage.setItem(BOOKMARK_KEY, JSON.stringify(bookmarks));
  }

  function isBookmarked(postId) {
    return getBookmarks().some(function (b) { return b.id === postId; });
  }

  function toggleBookmark(postId, title, url, image) {
    var bookmarks = getBookmarks();
    var idx = bookmarks.findIndex(function (b) { return b.id === postId; });
    if (idx >= 0) {
      bookmarks.splice(idx, 1);
      showToast('ブックマークを解除しました');
    } else {
      bookmarks.unshift({ id: postId, title: title, url: url, image: image });
      showToast('ブックマークに追加しました');
    }
    saveBookmarks(bookmarks);
    syncBookmarkButtons();
  }

  function syncBookmarkButtons() {
    document.querySelectorAll('.card__bookmark').forEach(function (btn) {
      var postId = btn.getAttribute('data-post-id');
      btn.classList.toggle('is-bookmarked', isBookmarked(postId));
    });
  }

  // Card bookmark clicks
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('.card__bookmark');
    if (!btn) return;
    e.preventDefault();
    e.stopPropagation();
    var postId = btn.getAttribute('data-post-id');
    var card = btn.closest('.card, article');
    var titleEl = card ? card.querySelector('.card__title a') : null;
    var imgEl = card ? card.querySelector('.card__image') : null;
    toggleBookmark(
      postId,
      titleEl ? titleEl.textContent : '',
      titleEl ? titleEl.href : '',
      imgEl ? imgEl.src : ''
    );
  });

  // Init bookmark state
  syncBookmarkButtons();

  // ========================================
  // Bookmark Modal
  // ========================================
  var bookmarkModal = document.getElementById('bookmark-modal');
  var bookmarkList = document.getElementById('bookmark-list');
  var bookmarkNav = document.getElementById('mobile-bookmark-nav');

  function openBookmarkModal() {
    if (!bookmarkModal) return;
    var bookmarks = getBookmarks();
    if (bookmarks.length === 0) {
      bookmarkList.innerHTML = '<p class="bookmark-modal__empty">まだ保存した記事がありません</p>';
    } else {
      var html = '';
      bookmarks.forEach(function (b) {
        html += '<a href="' + b.url + '" class="bookmark-modal__item">';
        if (b.image) {
          html += '<img src="' + b.image + '" class="bookmark-modal__item-img" alt="" loading="lazy">';
        }
        html += '<span class="bookmark-modal__item-title">' + b.title + '</span>';
        html += '</a>';
      });
      bookmarkList.innerHTML = html;
    }
    bookmarkModal.classList.add('is-open');
  }

  if (bookmarkNav) {
    bookmarkNav.addEventListener('click', function (e) {
      e.preventDefault();
      openBookmarkModal();
    });
  }

  if (bookmarkModal) {
    bookmarkModal.querySelector('.bookmark-modal__overlay').addEventListener('click', function () {
      bookmarkModal.classList.remove('is-open');
    });
    bookmarkModal.querySelector('.bookmark-modal__close').addEventListener('click', function () {
      bookmarkModal.classList.remove('is-open');
    });
  }

  // ========================================
  // Photo Lightbox
  // ========================================
  var lightbox = null;
  var lightboxImages = [];
  var lightboxIndex = 0;

  function createLightbox() {
    var el = document.createElement('div');
    el.className = 'lightbox';
    el.id = 'lightbox';
    el.innerHTML = '<button class="lightbox__close" aria-label="閉じる">&times;</button>'
      + '<button class="lightbox__nav lightbox__nav--prev" aria-label="前へ">&#10094;</button>'
      + '<img class="lightbox__img" src="" alt="">'
      + '<button class="lightbox__nav lightbox__nav--next" aria-label="次へ">&#10095;</button>';
    document.body.appendChild(el);

    el.querySelector('.lightbox__close').addEventListener('click', closeLightbox);
    el.querySelector('.lightbox__nav--prev').addEventListener('click', function () { navigateLightbox(-1); });
    el.querySelector('.lightbox__nav--next').addEventListener('click', function () { navigateLightbox(1); });
    el.addEventListener('click', function (e) {
      if (e.target === el) closeLightbox();
    });
    document.addEventListener('keydown', function (e) {
      if (!lightbox || !lightbox.classList.contains('is-open')) return;
      if (e.key === 'Escape') closeLightbox();
      if (e.key === 'ArrowLeft') navigateLightbox(-1);
      if (e.key === 'ArrowRight') navigateLightbox(1);
    });

    return el;
  }

  function openLightbox(src) {
    if (!lightbox) lightbox = createLightbox();
    lightboxImages = Array.from(document.querySelectorAll('.article-content img, .article-featured-image img'));
    lightboxIndex = lightboxImages.findIndex(function (img) { return img.src === src; });
    if (lightboxIndex < 0) lightboxIndex = 0;
    lightbox.querySelector('.lightbox__img').src = src;
    lightbox.classList.add('is-open');
    document.body.style.overflow = 'hidden';
  }

  function closeLightbox() {
    if (lightbox) {
      lightbox.classList.remove('is-open');
      document.body.style.overflow = '';
    }
  }

  function navigateLightbox(dir) {
    if (!lightboxImages.length) return;
    lightboxIndex = (lightboxIndex + dir + lightboxImages.length) % lightboxImages.length;
    lightbox.querySelector('.lightbox__img').src = lightboxImages[lightboxIndex].src;
  }

  // Bind article images to lightbox
  document.querySelectorAll('.article-content img, .article-featured-image img').forEach(function (img) {
    img.style.cursor = 'zoom-in';
    img.addEventListener('click', function () {
      openLightbox(this.src);
    });
  });

  // Content Slider はCSSアニメーション（無限スクロール）で動作。JSは不要。

  // ========================================
  // Load More (AJAX Pagination)
  // ========================================
  var loadMoreBtn = document.getElementById('load-more-btn');
  if (loadMoreBtn) {
    var currentPage = 1;
    var gridEl = document.querySelector('.grid.grid--3');

    loadMoreBtn.addEventListener('click', function () {
      currentPage++;
      loadMoreBtn.classList.add('is-loading');
      loadMoreBtn.textContent = '読み込み中...';

      var formData = new FormData();
      formData.append('action', 'knt_load_more');
      formData.append('nonce', typeof kntData !== 'undefined' ? kntData.nonce : '');
      formData.append('page', currentPage);
      formData.append('per_page', 9);

      var catMeta = document.querySelector('meta[name="knt-category-id"]');
      if (catMeta) formData.append('category', catMeta.content);

      var searchInput = document.querySelector('.archive-search__input');
      if (searchInput && searchInput.value) formData.append('search', searchInput.value);

      fetch(typeof kntData !== 'undefined' ? kntData.ajaxUrl : '/wp-admin/admin-ajax.php', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
      })
      .then(function (res) { return res.json(); })
      .then(function (data) {
        if (data.success && data.data.html) {
          var temp = document.createElement('div');
          temp.innerHTML = data.data.html;
          while (temp.firstChild) {
            gridEl.appendChild(temp.firstChild);
          }
          syncBookmarkButtons();
          initFadeUp();
        }
        if (!data.data.has_more) {
          loadMoreBtn.parentElement.remove();
        } else {
          loadMoreBtn.classList.remove('is-loading');
          loadMoreBtn.textContent = 'もっと見る';
        }
      })
      .catch(function () {
        loadMoreBtn.classList.remove('is-loading');
        loadMoreBtn.textContent = 'もっと見る';
        showToast('読み込みに失敗しました');
      });
    });
  }
  // ========================================
  // Food Quiz (食べたいもの診断)
  // ========================================
  var quizSteps = document.getElementById('quiz-steps');
  if (quizSteps) {
    var answers = {};
    var totalSteps = quizSteps.querySelectorAll('.food-quiz__step').length;
    var progressBar = document.getElementById('quiz-progress');

    // 結果マッピング
    var QUIZ_RESULTS = {
      // mood × who → genre + search keyword + description
      'gatturi_solo':     { genre: 'ラーメン', search: 'ラーメン', desc: '一人でサクッと食べられるラーメンがぴったり。こだわりの一杯を見つけよう！' },
      'gatturi_date':     { genre: '焼肉', search: '焼肉', desc: '二人で楽しむ焼肉デートはいかが？おしゃれな焼肉店を厳選。' },
      'gatturi_family':   { genre: '焼肉', search: '焼肉', desc: '家族みんなで楽しめる焼肉店。キッズメニューありのお店も。' },
      'gatturi_friends':  { genre: '焼肉・食べ放題', search: '焼肉', desc: '友達とがっつり焼肉！食べ放題や飲み放題付きのお店がおすすめ。' },
      'gatturi_business': { genre: '和食', search: '和食', desc: 'しっかり食べられる和食。接待にも使える落ち着いたお店を厳選。' },
      'assari_solo':      { genre: 'そば・うどん', search: '和食', desc: 'あっさりしたい気分なら、こだわりの蕎麦やうどんはいかが？' },
      'assari_date':      { genre: 'イタリアン', search: 'イタリアン', desc: 'あっさり派のデートにはイタリアンがぴったり。パスタやサラダが充実。' },
      'assari_family':    { genre: '和食', search: '和食', desc: '家族で落ち着いて食べられる和食。座敷席があるお店がおすすめ。' },
      'assari_friends':   { genre: 'カフェ', search: 'カフェ', desc: '友達とゆっくりカフェタイム。軽食も充実のおしゃれカフェを厳選。' },
      'assari_business':  { genre: '和食', search: '和食', desc: 'あっさりした和食ランチ。ビジネスシーンにも使えるお店を。' },
      'nomitai_solo':     { genre: '居酒屋', search: '居酒屋', desc: 'カウンターで一人飲み。気軽に入れる居酒屋を見つけよう。' },
      'nomitai_date':     { genre: 'ダイニングバー', search: 'デート', desc: '二人で楽しむおしゃれなバー。雰囲気の良いお店を厳選。' },
      'nomitai_family':   { genre: '居酒屋', search: '居酒屋', desc: '家族で楽しめるファミリー居酒屋。お子様メニューありのお店も。' },
      'nomitai_friends':  { genre: '居酒屋・飲み放題', search: '居酒屋', desc: '飲み放題付きコースで盛り上がろう！幹事さん必見のお店を厳選。' },
      'nomitai_business': { genre: '個室居酒屋', search: '接待', desc: '接待にも使える個室居酒屋。落ち着いた雰囲気のお店を。' },
      'mattari_solo':     { genre: 'カフェ', search: 'カフェ', desc: '一人でまったりできるカフェ。Wi-Fiや電源ありのお店も。' },
      'mattari_date':     { genre: 'カフェ', search: 'カフェ', desc: 'デートにぴったりのおしゃれカフェ。スイーツが自慢のお店を厳選。' },
      'mattari_family':   { genre: 'カフェ', search: 'カフェ', desc: '家族でゆっくりできるカフェ。キッズスペースありのお店も。' },
      'mattari_friends':  { genre: 'カフェ', search: 'カフェ', desc: '友達とまったりおしゃべり。長居OKのカフェを集めました。' },
      'mattari_business': { genre: 'カフェ', search: 'カフェ', desc: '打ち合わせにも使えるカフェ。静かで落ち着いた空間を。' },
      'waiwai_solo':      { genre: 'ラーメン', search: 'ラーメン', desc: '活気あるラーメン店でエネルギーチャージ！' },
      'waiwai_date':      { genre: 'イタリアン', search: 'イタリアン', desc: '二人で楽しむカジュアルイタリアン。ピザやパスタをシェアして。' },
      'waiwai_family':    { genre: 'ファミリーレストラン', search: '子連れ', desc: '家族みんなで楽しめるレストラン。メニュー豊富なお店を厳選。' },
      'waiwai_friends':   { genre: '居酒屋・飲み放題', search: '居酒屋', desc: 'みんなで盛り上がれる居酒屋！飲み放題付きコースがおすすめ。' },
      'waiwai_business':  { genre: '居酒屋', search: '飲み会', desc: '会社の飲み会にぴったり！大人数対応の居酒屋を厳選。' },
    };

    quizSteps.addEventListener('click', function(e) {
      var btn = e.target.closest('.food-quiz__option');
      if (!btn) return;

      var key = btn.getAttribute('data-key');
      var value = btn.getAttribute('data-value');
      answers[key] = value;

      var currentStep = btn.closest('.food-quiz__step');
      var stepNum = parseInt(currentStep.getAttribute('data-step'));

      // 進捗バー更新
      progressBar.style.width = ((stepNum / totalSteps) * 100) + '%';

      if (stepNum < totalSteps) {
        // 次のステップへ
        currentStep.classList.remove('is-active');
        var next = quizSteps.querySelector('[data-step="' + (stepNum + 1) + '"]');
        if (next) next.classList.add('is-active');
      } else {
        // 結果表示
        showQuizResult();
      }
    });

    function showQuizResult() {
      var resultKey = answers.mood + '_' + answers.who;
      var result = QUIZ_RESULTS[resultKey] || { genre: 'グルメ', search: 'グルメ', desc: 'あなたにぴったりのお店を探してみましょう！' };

      document.getElementById('quiz-result-genre').textContent = result.genre;
      document.getElementById('quiz-result-desc').textContent = result.desc;

      var base = (typeof kntMapData !== 'undefined' && kntMapData.homeUrl) ? kntMapData.homeUrl : window.location.origin;
      document.getElementById('quiz-result-link').href = base + '/?s=' + encodeURIComponent(result.search);

      quizSteps.style.display = 'none';
      document.getElementById('quiz-result').style.display = 'block';
      progressBar.style.width = '100%';
    }

    // リトライ
    var retryBtn = document.getElementById('quiz-retry');
    if (retryBtn) {
      retryBtn.addEventListener('click', function() {
        answers = {};
        quizSteps.style.display = 'block';
        document.getElementById('quiz-result').style.display = 'none';
        quizSteps.querySelectorAll('.food-quiz__step').forEach(function(s) { s.classList.remove('is-active'); });
        quizSteps.querySelector('[data-step="1"]').classList.add('is-active');
        progressBar.style.width = '0%';
      });
    }
  }
})();
