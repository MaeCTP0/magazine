document.addEventListener('DOMContentLoaded', function() {
    // Универсальная функция для инициализации слайдеров
    function initSlider(sliderClass, prevBtnClass, nextBtnClass) {
        const slider = document.querySelector(`.${sliderClass}`);
        if (!slider) return;
        
        const track = slider.querySelector(`.${sliderClass}-track`) || slider.querySelector('.slider-track') || slider.querySelector('.reviews-track');
        const prevBtn = slider.querySelector(`.${prevBtnClass}`) || slider.querySelector('.slider-prev') || slider.querySelector('.reviews-prev');
        const nextBtn = slider.querySelector(`.${nextBtnClass}`) || slider.querySelector('.slider-next') || slider.querySelector('.reviews-next');
        const slides = track.querySelectorAll('.slide, .review-card');
        
        if (!track || !prevBtn || !nextBtn || slides.length === 0) return;
        
        const slideWidth = slides[0].offsetWidth;
        const gap = parseInt(window.getComputedStyle(track).gap) || 20;
        const scrollAmount = slideWidth + gap;
        
        // Обработчики для кнопок
        nextBtn.addEventListener('click', () => {
            track.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        });
        
        prevBtn.addEventListener('click', () => {
            track.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        });
        
        // Отключение кнопок в крайних положениях
        function updateButtons() {
            prevBtn.disabled = track.scrollLeft <= 10;
            nextBtn.disabled = track.scrollLeft >= track.scrollWidth - track.clientWidth - 10;
        }
        
        track.addEventListener('scroll', updateButtons);
        updateButtons();
        
        // Адаптация к изменению размера окна
        window.addEventListener('resize', () => {
            const newSlideWidth = slides[0].offsetWidth;
            if (newSlideWidth !== slideWidth) {
                track.scrollLeft = track.scrollLeft * (newSlideWidth / slideWidth);
            }
        });
    }
    
    // Инициализация слайдеров
    initSlider('products-slider', 'slider-prev', 'slider-next');
    initSlider('reviews-slider', 'reviews-prev', 'reviews-next');
    
    // Дополнительные улучшения для слайдеров
    document.querySelectorAll('.slider-track, .reviews-track').forEach(track => {
        // Плавный скролл при перетаскивании мышью
        let isDown = false;
        let startX;
        let scrollLeft;
        
        track.addEventListener('mousedown', (e) => {
            isDown = true;
            track.style.cursor = 'grabbing';
            startX = e.pageX - track.offsetLeft;
            scrollLeft = track.scrollLeft;
        });
        
        track.addEventListener('mouseleave', () => {
            isDown = false;
            track.style.cursor = 'grab';
        });
        
        track.addEventListener('mouseup', () => {
            isDown = false;
            track.style.cursor = 'grab';
        });
        
        track.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - track.offsetLeft;
            const walk = (x - startX) * 2;
            track.scrollLeft = scrollLeft - walk;
        });
        
        // Стиль курсора
        track.style.cursor = 'grab';
    });
});