import './bootstrap';
import Alpine from 'alpinejs';
import * as lucide from 'lucide';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

// Extract createIcons and all individual icon objects
const { createIcons, icons: bundleIcons, ...allIcons } = lucide;
const iconsMap = bundleIcons || allIcons;

// Initialize Lucide icons helper
window.lucide = window.lucide || lucide;
window.initLucide = function() {
    try {
        if (typeof createIcons === 'function' && Object.keys(iconsMap).length > 0) {
            createIcons({ 
                icons: iconsMap,
                attrs: {
                    'stroke-width': 1.9,
                    'stroke-linecap': 'round',
                    'stroke-linejoin': 'round'
                }
            });
        } else if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons({
                attrs: {
                    'stroke-width': 1.9,
                    'stroke-linecap': 'round',
                    'stroke-linejoin': 'round'
                }
            });
        }
    } catch (e) {
        console.warn('Lucide init fallback:', e);
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    }
};

// Start Alpine & Lucide on DOM load
document.addEventListener('DOMContentLoaded', () => {
    window.initLucide();
    Alpine.start();

    // Setup intersection observer for scroll reveal
    const revealElements = document.querySelectorAll('.reveal');
    if (revealElements.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -40px 0px'
        });

        revealElements.forEach(el => observer.observe(el));
    }
});
