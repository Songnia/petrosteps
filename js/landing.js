(function () {
  'use strict';

  var header = document.querySelector('[data-header]');
  var menuButton = document.querySelector('[data-menu-button]');
  var mobileNav = document.querySelector('[data-mobile-nav]');

  if (header && 'IntersectionObserver' in window) {
    var topMarker = document.createElement('span');
    topMarker.setAttribute('aria-hidden', 'true');
    topMarker.style.position = 'absolute';
    topMarker.style.top = '0';
    document.body.prepend(topMarker);
    new IntersectionObserver(function (entries) {
      header.classList.toggle('is-scrolled', !entries[0].isIntersecting);
    }).observe(topMarker);
  }

  function closeMenu() {
    if (!menuButton || !mobileNav) return;
    menuButton.setAttribute('aria-expanded', 'false');
    menuButton.setAttribute('aria-label', 'Ouvrir le menu');
    mobileNav.classList.remove('is-open');
    document.body.classList.remove('menu-open');
  }

  if (menuButton && mobileNav) {
    menuButton.addEventListener('click', function () {
      var shouldOpen = menuButton.getAttribute('aria-expanded') !== 'true';
      menuButton.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
      menuButton.setAttribute('aria-label', shouldOpen ? 'Fermer le menu' : 'Ouvrir le menu');
      mobileNav.classList.toggle('is-open', shouldOpen);
      document.body.classList.toggle('menu-open', shouldOpen);
    });
    mobileNav.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', closeMenu);
    });
  }

  var revealItems = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    var revealObserver = new IntersectionObserver(function (entries, observer) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12 });
    revealItems.forEach(function (item) { revealObserver.observe(item); });
  } else {
    revealItems.forEach(function (item) { item.classList.add('is-visible'); });
  }
}());
