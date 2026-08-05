document.addEventListener('DOMContentLoaded', () => {
  // Footer year
  const yearEl = document.getElementById('current-year');
  if (yearEl) {
    yearEl.textContent = new Date().getFullYear();
  }

  // Mobile menu toggle
  const menuBtn = document.querySelector('.mobile-menu');
  const navLinks = document.querySelector('.nav-links');
  if (menuBtn && navLinks) {
    menuBtn.addEventListener('click', () => {
      const expanded = menuBtn.getAttribute('aria-expanded') === 'true';
      menuBtn.setAttribute('aria-expanded', String(!expanded));
      menuBtn.classList.toggle('active');
      navLinks.classList.toggle('hidden');
    });

    navLinks.querySelectorAll('a').forEach((link) => {
      link.addEventListener('click', () => {
        menuBtn.setAttribute('aria-expanded', 'false');
        menuBtn.classList.remove('active');
        navLinks.classList.add('hidden');
      });
    });
  }

  // Smooth-scroll for in-page anchor links (About, Member NGOs, etc).
  // Deliberately self-contained: it doesn't matter what URL the link's
  // href actually is (a bare "#about" or a full ".../home#about") — we
  // read only the fragment, and if that element exists on the CURRENT
  // page we scroll to it ourselves and cancel the browser's default jump.
  // If it doesn't exist here (we're on a different page), we let it fall
  // through to a normal navigation to the home page's anchored URL.
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  document.querySelectorAll('a[href*="#"]').forEach((link) => {
    link.addEventListener('click', (event) => {
      const href = link.getAttribute('href') || '';
      const hashIndex = href.indexOf('#');
      if (hashIndex === -1) return;

      const id = href.slice(hashIndex + 1);
      if (!id) return;

      const target = document.getElementById(id);
      if (!target) return; // not on this page — let the browser navigate normally

      event.preventDefault();
      target.scrollIntoView({
        behavior: prefersReducedMotion ? 'auto' : 'smooth',
        block: 'start',
      });
      history.pushState(null, '', '#' + id);
    });
  });
});
