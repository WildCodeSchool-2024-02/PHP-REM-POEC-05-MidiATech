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

    document.getElementById('loginButton').addEventListener('click', function () {


        alert('Login successful!');
        document.getElementById('popup').classList.remove('active');
        document.getElementById('overlay').classList.remove('active');
    });
});
