// popup.js

document.addEventListener('DOMContentLoaded', function() {
    // Script pour gérer le pop-up
    document.getElementById('accountIcon').addEventListener('click', function (e) {
        e.preventDefault();
        document.getElementById('popup').classList.add('active');
        document.getElementById('overlay').classList.add('active');
    });

    document.getElementById('closePopupBtn').addEventListener('click', function () {
        document.getElementById('popup').classList.remove('active');
        document.getElementById('overlay').classList.remove('active');
    });

    document.getElementById('overlay').addEventListener('click', function () {
        document.getElementById('popup').classList.remove('active');
        document.getElementById('overlay').classList.remove('active');
    });

    document.getElementById('loginButton').addEventListener('click', function (event) {
        event.preventDefault(); // Empêche le comportement par défaut du bouton de soumission
    
        document.getElementById('loginForm').submit(); // Soumet le formulaire
    });
    
});
