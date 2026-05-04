document.addEventListener('DOMContentLoaded', () => {
  const heroPhoto = document.querySelector('.menu-hero-photo');
  const heroVideo = document.querySelector('.menu-hero-video');

  function handleMenuHero() {
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

  handleMenuHero();
  window.addEventListener('resize', handleMenuHero);

  const menuItems = document.querySelectorAll('.menu-item');
  if (menuItems.length > 0) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const index = Array.from(menuItems).indexOf(entry.target);
          entry.target.style.animationDelay = `${index * 0.07}s`;
          entry.target.classList.add('visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12 });
    menuItems.forEach(item => observer.observe(item));
  }
});
