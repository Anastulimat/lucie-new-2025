import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';


// DOM elements
const body = document.body;
const menuToggle = document.querySelector('.menu-toggle');
const navMenu = document.querySelector('nav ul');
const galleryItems = document.querySelectorAll('.gallery-item');
const lightbox = document.querySelector('.lightbox');
const lightboxImg = document.querySelector('.lightbox-content img');
const closeLightbox = document.querySelector('.close-lightbox');
const prevImage = document.querySelector('.prev-image');
const nextImage = document.querySelector('.next-image');

// Current image index for lightbox
let currentIndex = 0;

// Gallery images array
const galleryImages = Array.from(document.querySelectorAll('.gallery-item img'));

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
menuToggle.addEventListener('click', () => {
    navMenu.classList.toggle('show');
});

// Close mobile menu when clicking outside
document.addEventListener('click', (e) => {
    if (!e.target.closest('nav') && !e.target.closest('.menu-toggle')) {
        navMenu.classList.remove('show');
    }
});

// Lightbox functionality
galleryItems.forEach(item => {
    const img = item.querySelector('img');

    img.addEventListener('click', () => {
        currentIndex = parseInt(img.getAttribute('data-index'));

        lightboxImg.src = img.src;
        lightboxImg.alt = img.alt;

        lightbox.classList.add('active');
        document.body.style.overflow = 'hidden'; // Prevent scrolling when lightbox is open
    });
});

// Close lightbox
closeLightbox.addEventListener('click', () => {
    lightbox.classList.remove('active');
    document.body.style.overflow = ''; // Restore scrolling
});

// Close lightbox when clicking outside the image
lightbox.addEventListener('click', (e) => {
    if (e.target === lightbox) {
        lightbox.classList.remove('active');
        document.body.style.overflow = '';
    }
});

// Navigate through images
prevImage.addEventListener('click', () => {
    currentIndex = (currentIndex - 1 + galleryImages.length) % galleryImages.length;
    const img = galleryImages[currentIndex];
    lightboxImg.src = img.src;
    lightboxImg.alt = img.alt;
});

nextImage.addEventListener('click', () => {
    currentIndex = (currentIndex + 1) % galleryImages.length;
    const img = galleryImages[currentIndex];
    lightboxImg.src = img.src;
    lightboxImg.alt = img.alt;
});

// Keyboard navigation for lightbox
document.addEventListener('keydown', (e) => {
    if (!lightbox.classList.contains('active')) return;

    if (e.key === 'Escape') {
        lightbox.classList.remove('active');
        document.body.style.overflow = '';
    } else if (e.key === 'ArrowLeft') {
        currentIndex = (currentIndex - 1 + galleryImages.length) % galleryImages.length;
        const img = galleryImages[currentIndex];
        lightboxImg.src = img.src;
        lightboxImg.alt = img.alt;
    } else if (e.key === 'ArrowRight') {
        currentIndex = (currentIndex + 1) % galleryImages.length;
        const img = galleryImages[currentIndex];
        lightboxImg.src = img.src;
        lightboxImg.alt = img.alt;
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
