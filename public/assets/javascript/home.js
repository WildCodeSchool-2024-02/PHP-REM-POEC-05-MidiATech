// gestion images
const img = document.querySelectorAll('.slide_picture');
for (let i = 0; i < img.length; i++) {
  if (img[i].width < img[i].height) {
    img[i].style.height = "70vh";
    img[i].style.width = "auto";
    // img[i].style.max-width = "70vw";
    // img[i].style.max-height = "70vw";
  }
}


// carrousel
const slidesContainer = document.getElementById("slides_container");
const slide = document.querySelector(".slide");
const prevButton = document.getElementById("fleche_gauche");
const nextButton = document.getElementById("fleche_droite");
nextButton.addEventListener("click", () => {
  const slideWidth = slide.clientWidth;
  slidesContainer.scrollLeft += slideWidth;
});
prevButton.addEventListener("click", () => {
  const slideWidth = slide.clientWidth;
  slidesContainer.scrollLeft -= slideWidth;
});