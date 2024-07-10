let goToTop = document.getElementById("goToTop");

window.onscroll = function () {
    scrollFunction();
};

function scrollFunction()
{
    if (document.documentElement.scrollTop > 200 && window.innerWidth > 768) {
        goToTop.style.display = "flex";
    } else if (
        document.documentElement.scrollTop <= 200 &&
        window.innerWidth > 768
    ) {
        goToTop.style.display = "none";
    } else if (
        document.documentElement.scrollTop > 110 &&
        window.innerWidth <= 768
    ) {
        goToTop.style.display = "flex";
    } else if (
        document.documentElement.scrollTop <= 109 &&
        window.innerWidth <= 768
    ) {
        goToTop.style.display = "none";
    }
}

function topFunction()
{
    document.body.scrollTop = 0; // For Safari
    document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
}
