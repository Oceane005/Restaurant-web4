// ============================================
// NAVIGATION OVERLAY — nav.js
// ============================================

document.addEventListener('DOMContentLoaded', () => {

  const navOverlay = document.getElementById('navOverlay');
  const navClose   = document.getElementById('navClose');
  const burger     = document.querySelector('.burger');

  // Ouvrir le menu
  window.openNav = function() {
    navOverlay.classList.add('open');
    navOverlay.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }

  // Fermer le menu
  window.closeNav = function() {
    navOverlay.classList.remove('open');
    navOverlay.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  // Burger ouvre le menu
  if (burger) {
    burger.addEventListener('click', window.openNav);
  }

  // Bouton X ferme le menu
  if (navClose) {
    navClose.addEventListener('click', window.closeNav);
  }

  // Fermer en cliquant sur un lien
  document.querySelectorAll('.nav-link, .nav-reserver').forEach(link => {
    link.addEventListener('click', window.closeNav);
  });

  // Fermer avec Escape
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') window.closeNav();
  });

});