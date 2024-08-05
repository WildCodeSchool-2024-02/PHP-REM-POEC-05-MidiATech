document.addEventListener("DOMContentLoaded", function () {
    const errorModal = document.getElementById("errorModal");
    const closeModal = document.getElementById("closeModal");

    if (errorModal) {
        errorModal.style.display = "block";

        closeModal.onclick = function () {
            errorModal.style.display = "none";
        };

        window.onclick = function (event) {
            if (event.target === errorModal) {
                errorModal.style.display = "none";
            }
        };
    }
});
