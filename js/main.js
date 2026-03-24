/**
 * KNT Media - Main JavaScript
 */
(function () {
  'use strict';

  // ========================================
  // Mobile Menu Toggle
  // ========================================
  const menuToggle = document.querySelector('.menu-toggle');
  const mobileMenu = document.getElementById('mobile-menu');

  if (menuToggle && mobileMenu) {
    menuToggle.addEventListener('click', function () {
      const isOpen = mobileMenu.classList.toggle('is-open');
      menuToggle.classList.toggle('is-active');
      menuToggle.setAttribute('aria-expanded', isOpen);
      menuToggle.setAttribute('aria-label', isOpen ? 'メニューを閉じる' : 'メニューを開く');
    });
  }

  // ========================================
  // FadeUp on Scroll (Intersection Observer)
  // ========================================
  const fadeElements = document.querySelectorAll('.fadeup');

  if (fadeElements.length > 0 && 'IntersectionObserver' in window) {
    const observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
          }
        });
      },
      {
        threshold: 0.1,
        rootMargin: '0px 0px -40px 0px',
      }
    );

    fadeElements.forEach(function (el) {
      observer.observe(el);
    });
  } else {
    // Fallback: show all elements immediately
    fadeElements.forEach(function (el) {
      el.classList.add('is-visible');
    });
  }

  // ========================================
  // Table of Contents Toggle
  // ========================================
  const tocToggle = document.getElementById('toc-toggle');
  const tocList = document.getElementById('toc-list');

  if (tocToggle && tocList) {
    tocToggle.addEventListener('click', function () {
      const isHidden = tocList.style.display === 'none';
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
          ? document.querySelector('.site-header').offsetHeight
          : 0;
        var targetPosition = target.getBoundingClientRect().top + window.pageYOffset - headerHeight - 16;

        window.scrollTo({
          top: targetPosition,
          behavior: 'smooth',
        });
      }
    });
  });

  // ========================================
  // Header shrink on scroll
  // ========================================
  var header = document.querySelector('.site-header');
  if (header) {
    var lastScroll = 0;
    window.addEventListener(
      'scroll',
      function () {
        var currentScroll = window.pageYOffset;
        if (currentScroll > 100) {
          header.style.boxShadow = '0 2px 20px rgba(0,0,0,0.06)';
        } else {
          header.style.boxShadow = 'none';
        }
        lastScroll = currentScroll;
      },
      { passive: true }
    );
  }
})();
