// carousel component
let Carousel = (function () {
    const slidesContainer = document.getElementById("slides_container");
    const slideWidth = document.querySelector(".slide").clientWidth;
    const indicators = Array.from(document.querySelectorAll('.indicator')); // converting NodeList to Array for easier handling
    let currentIndex = 0; // initial active slide

    indicators[currentIndex].classList.add('active'); // set class to initial active slide

    // private function to update active class on indicators
    function updateActiveIndicator()
    {
        indicators.forEach(indicator => indicator.classList.remove('active')); // remove class from all indicators
        indicators[currentIndex].classList.add('active'); // add class to current active indicator
    }

    let startingX;

// Listener for touchstart event
    slidesContainer.addEventListener('touchstart', function (e) {
        startingX = e.touches[0].clientX;  // Get the original touch position.
    });

// Listener for touchmove event
    slidesContainer.addEventListener('touchmove', function (e) {
        let touch = e.touches[0];  // Get the information for finger #1
        let change = startingX - touch.clientX;

        if (change < 0) {
            // right swipe, show previous
            Carousel.moveSlide(-1);
        } else {
            // left swipe, show next
            Carousel.moveSlide(1);
        }

        // Save the new startingX to start over.
        startingX = touch.clientX;
    });

    function moveSlide(indexChange)
    {
        currentIndex = (currentIndex + indexChange + indicators.length) % indicators.length;
        slidesContainer.scrollLeft = currentIndex === 0 ? 0 : slideWidth * currentIndex;

        updateActiveIndicator(); // update indicators
    }

    // Setting up the event listeners
    document.getElementById("fleche_gauche").addEventListener("click", () => moveSlide(-1));
    document.getElementById("fleche_droite").addEventListener("click", () => moveSlide(1));

    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
            currentIndex = index; // update current index
            slidesContainer.scrollLeft = slideWidth * currentIndex;
            updateActiveIndicator(); // update indicators
        });
    });

    document.addEventListener('keydown', function (event) {
        switch (event.key) {
            case 'ArrowRight': moveSlide(1); break;
            case 'ArrowLeft': moveSlide(-1); break;
        }
    });

    return {
        moveSlide: moveSlide
    }
})();

document.addEventListener('DOMContentLoaded', function() {
    // Get all modifier buttons
    const modifierButtons = document.querySelectorAll('.modifier-button');

    modifierButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Get the target form id
            const targetFormId = button.getAttribute('data-target');
            // Toggle the display of the form
            const form = document.getElementById(targetFormId);
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        });
    });
});

// Toggle form visibility
document.querySelectorAll('.modifier-button').forEach(button => {
    button.addEventListener('click', function() {
        const targetId = this.getAttribute('data-target');
        toggleForm(targetId);
    });
});

function toggleForm(formId) {
    const form = document.getElementById(formId);
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}

// Toggle form visibility
document.querySelectorAll('.modifier-button').forEach(button => {
    button.addEventListener('click', function() {
        const target = document.getElementById(this.getAttribute('data-target'));
        target.style.display = target.style.display === 'none' ? 'block' : 'none';
    });
});