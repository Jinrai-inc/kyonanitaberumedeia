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
})();
