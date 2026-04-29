/* ============================================================
   assets/js/landing.js — SUKATANI Landing Page Scripts
   ============================================================ */

// Navbar shadow saat scroll
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
    navbar.classList.toggle('scrolled', window.scrollY > 30);
});

// Scroll reveal dengan IntersectionObserver
const revealEls = document.querySelectorAll('.reveal');
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            observer.unobserve(entry.target);
        }
    });
}, { threshold: 0.15 });

revealEls.forEach(el => observer.observe(el));