// ============================================
// IROSHI TAKAHASHI — main.js
// ============================================

document.addEventListener('DOMContentLoaded', () => {

  // ── HEADER : fond opaque au scroll ──
  const header = document.querySelector('.header');

  // ── HERO : vidéo sur tablet+, photo sur mobile ──
  const heroPhoto = document.querySelector('.hero-photo');
  const heroVideo = document.querySelector('.hero-video');

  console.log('heroPhoto:', heroPhoto);
  console.log('heroVideo:', heroVideo);
  console.log('window.innerWidth:', window.innerWidth);

  function showVideo() {
    console.log('showVideo() appelé');
    heroPhoto.style.display = 'none';
    heroVideo.style.display = 'block';
    heroVideo.load();
    const playPromise = heroVideo.play();
    if (playPromise !== undefined) {
      playPromise
        .then(() => console.log('Vidéo en lecture'))
        .catch(err => console.warn('Autoplay bloqué:', err));
    }
  }

  function showPhoto() {
    console.log('showPhoto() appelé');
    heroVideo.style.display = 'none';
    heroVideo.pause();
    heroPhoto.style.display = 'block';
  }

  function handleHero() {
    if (window.innerWidth >= 768) {
      showVideo();
    } else {
      showPhoto();
    }
  }

  if (heroVideo && heroPhoto) {
    handleHero();
    window.addEventListener('resize', handleHero);
  } else {
    console.error('Éléments hero introuvables dans le DOM');
  }

  // ── HEADER : fond opaque au scroll ──
  if (header) {
    window.addEventListener('scroll', () => {
      if (window.scrollY > 60) {
        header.style.background = 'rgba(6,7,15,0.97)';
      } else {
        header.style.background = 'linear-gradient(180deg, rgba(6,7,15,0.92) 0%, transparent 100%)';
      }
    });
  }

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