document.addEventListener('DOMContentLoaded', () => {
  const navOverlay = document.getElementById('navOverlay');
  const navClose   = document.getElementById('navClose');
  const burger     = document.querySelector('.burger');

  window.openNav = function() {
    navOverlay.classList.add('open');
    navOverlay.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }

  window.closeNav = function() {
    navOverlay.classList.remove('open');
    navOverlay.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  if (burger)   burger.addEventListener('click', window.openNav);
  if (navClose) navClose.addEventListener('click', window.closeNav);

  document.querySelectorAll('.nav-link, .nav-reserver').forEach(link => {
    link.addEventListener('click', window.closeNav);
  });

  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') window.closeNav();
  });
});
