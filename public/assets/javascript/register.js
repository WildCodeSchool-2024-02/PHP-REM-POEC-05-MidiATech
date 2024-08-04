document.addEventListener("DOMContentLoaded", function() {
    const modal = document.getElementById("errorModal");
    const closeModal = document.getElementById("closeModal");

    if (modal) {
        modal.style.display = "block";

        closeModal.onclick = function() {
            modal.style.display = "none";
        };

        window.onclick = function(event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        };
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector(".form");
    const passwordInput = document.getElementById("password");
    const confirmPasswordInput = document.getElementById("confirmPassword");
    const errorMessage = document.createElement("p");

    errorMessage.className = "error";
    errorMessage.style.display = "none"; // Initially hidden
    form.insertBefore(errorMessage, form.querySelector(".submit"));

    form.addEventListener("submit", function (event) {
        // Clear previous error messages
        errorMessage.style.display = "none";

        // Check if passwords match
        if (passwordInput.value !== confirmPasswordInput.value) {
            event.preventDefault(); // Stop form submission
            errorMessage.textContent = "Les mots de passe ne correspondent pas.";
            errorMessage.style.display = "block"; // Show error message
            confirmPasswordInput.focus(); // Focus on the confirm password field
        }
    });
});
