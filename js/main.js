document.addEventListener('DOMContentLoaded', () => {

  // ── HEADER : fond opaque au scroll ──
  const header = document.querySelector('.header');
  if (header) {
    window.addEventListener('scroll', () => {
      header.style.background = window.scrollY > 60
        ? 'rgba(6,7,15,0.97)'
        : 'linear-gradient(180deg, rgba(6,7,15,0.92) 0%, transparent 100%)';
    });
  }

  // ── HERO : vidéo sur tablet+, photo sur mobile ──
  const heroPhoto = document.querySelector('.hero-photo');
  const heroVideo = document.querySelector('.hero-video');

  function handleHero() {
    if (!heroPhoto || !heroVideo) return;
    if (window.innerWidth >= 768) {
      heroPhoto.style.display = 'none';
      heroVideo.style.display = 'block';
      heroVideo.load();
      heroVideo.play().catch(() => {});
    } else {
      heroVideo.style.display = 'none';
      heroVideo.pause();
      heroPhoto.style.display = 'block';
    }
  }

  handleHero();
  window.addEventListener('resize', handleHero);

  // ── FADE-IN au scroll ──
  const fadeEls = document.querySelectorAll(
    '.chef-title-block, .chef-body, .chef-desc, .jp-title, .latin-title, .philosophie p, .histoire-inner, .reseaux-title, .lanternes-scene, .contact-label, .contact-inner'
  );

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.15 });

  fadeEls.forEach(el => {
    el.classList.add('fade-in');
    observer.observe(el);
  });

});
