import './bootstrap.js';
import './styles/app.css';

// DOM elements
const body = document.body;
const menuToggle = document.querySelector('.menu-toggle');
const navMenu = document.querySelector('nav ul');
const galleryItems = document.querySelectorAll('.gallery-item');

// Add loaded class to body when page is fully loaded
window.addEventListener('load', () => {
    // Fade in gallery items one by one
    galleryItems.forEach((item, index) => {
        setTimeout(() => {
            item.classList.add('visible');
        }, 100 * index);
    });
});

// Mobile menu toggle
menuToggle?.addEventListener('click', () => {
    navMenu?.classList.toggle('show');
});

// Close mobile menu when clicking outside
document.addEventListener('click', (e) => {
    if (!e.target.closest('nav') && !e.target.closest('.menu-toggle')) {
        navMenu?.classList.remove('show');
    }
});

// Intersection Observer for lazy loading and scroll animations
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            observer.unobserve(entry.target);
        }
    });
}, {
    threshold: 0.1
});

galleryItems.forEach(item => {
    observer.observe(item);
});

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
