
    document.addEventListener("DOMContentLoaded", function() {
        const slidesContainer = document.getElementById('slides_container');
        const slides = document.querySelectorAll('.slide');
        const prevButton = document.getElementById('fleche_gauche');
        const nextButton = document.getElementById('fleche_droite');
        let currentSlideIndex = 0;

        function showSlide(index) {
            slidesContainer.scrollTo({
                left: slides[index].offsetLeft,
                behavior: 'smooth'
            });
        }

        function showNextSlide() {
            currentSlideIndex = (currentSlideIndex + 1) % slides.length;
            showSlide(currentSlideIndex);
        }

        function showPrevSlide() {
            currentSlideIndex = (currentSlideIndex - 1 + slides.length) % slides.length;
            showSlide(currentSlideIndex);
        }

        nextButton.addEventListener('click', showNextSlide);
        prevButton.addEventListener('click', showPrevSlide);
    });
