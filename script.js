// Main Script for Interactions

document.addEventListener('DOMContentLoaded', () => {
    // Console log for interactions
    const buttons = document.querySelectorAll('button, a');
    buttons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            // Simple visual feedback
            if (e.target.tagName === 'BUTTON' && !e.target.classList.contains('slider-btn')) {
                e.target.style.transform = 'scale(0.95)';
                setTimeout(() => e.target.style.transform = 'scale(1)', 100);
            }
        });
    });

    // Mobile Menu Toggle logic
    const hamburgerBtn = document.querySelector('.hamburger-btn');
    const closeMenuBtn = document.querySelector('.close-menu-btn');
    const mobileMenuOverlay = document.querySelector('.mobile-menu-overlay');

    if (hamburgerBtn && mobileMenuOverlay && closeMenuBtn) {
        hamburgerBtn.addEventListener('click', () => {
            mobileMenuOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        });

        closeMenuBtn.addEventListener('click', () => {
            mobileMenuOverlay.classList.remove('active');
            document.body.style.overflow = '';
        });

        const mobileLinks = mobileMenuOverlay.querySelectorAll('a');
        mobileLinks.forEach(link => {
            link.addEventListener('click', () => {
                mobileMenuOverlay.classList.remove('active');
                document.body.style.overflow = '';
            });
        });
    }

    // Thumbnail logic if on product page
    const thumbs = document.querySelectorAll('.thumb');

});

// Global functions for slider - moved outside to ensure accessibility
window.changeImage = function (imageSrc, index) {
    const mainImg = document.getElementById('mainImage');
    const thumbs = document.querySelectorAll('.thumb');

    if (mainImg) {
        mainImg.style.opacity = '0';
        setTimeout(() => {
            mainImg.src = imageSrc;
            mainImg.dataset.index = index;
            mainImg.style.opacity = '1';
        }, 150);
    }

    // Update active thumb
    if (thumbs.length > 0) {
        thumbs.forEach(t => t.classList.remove('active'));
        if (thumbs[index]) {
            thumbs[index].classList.add('active');
        }
    }
};

window.moveSlider = function (direction) {
    console.log('moveSlider called with direction:', direction);
    const mainImg = document.getElementById('mainImage');
    const images = window.productImages;
    console.log('productImages:', images);

    if (!mainImg || !images || images.length === 0) {
        console.error('Missing mainImg or images');
        return;
    }

    let currentIndex = parseInt(mainImg.dataset.index || 0);
    console.log('Current Index:', currentIndex);

    // Add logic to handle negative modulo correctly in JS
    let newIndex = (currentIndex + direction) % images.length;
    if (newIndex < 0) newIndex += images.length;

    console.log('New Index:', newIndex);

    changeImage(images[newIndex], newIndex);
};


