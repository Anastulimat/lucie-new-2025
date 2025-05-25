import './bootstrap.js';
import './styles/app.css';

const menuToggle = document.querySelector('.menu-toggle');
const navMenu = document.querySelector('nav ul');

menuToggle?.addEventListener('click', () => {
    navMenu?.classList.toggle('show');
});

document.addEventListener('click', (e) => {
    if (!e.target.closest('nav') && !e.target.closest('.menu-toggle')) {
        navMenu.classList.remove('show');
    }
});


console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
