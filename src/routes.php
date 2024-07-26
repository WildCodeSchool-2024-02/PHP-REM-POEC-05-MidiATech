<?php

// list of accessible routes of your application, add every new route here
// key : route to match
// values : 1. controller name
//          2. method name
//          3. (optional) array of query string keys to send as parameter to the method
// e.g route '/item/edit?id=1' will execute $itemController->edit(1)
return [
    '' => ['HomeController', 'index'],
    'search' => ['HomeController', 'search'],
    'books' => ['BooksController', 'index', ['category']],
    'musics' => ['MusicsController', 'index', ['category']],
    'videos' => ['VideosController', 'index', ['category', 'type']],
    'login' => ['UserController', 'login'],
    'register' => ['UserController', 'register'],
    'account' => ['UserController', 'index'],
    'admin' => ['AdminController', 'index'],
    'logout' => ['UserController', 'logout'],
    'books/show' => ['BooksController', 'show', ['id']],
    'musics/show' => ['MusicsController', 'show', ['id']],
    'videos/show' => ['VideosController', 'show', ['id']],
    'books/add' => ['BooksController', 'add'],
    'videos/add' => ['VideosController', 'add'],
    'musics/add' => ['MusicsController', 'add'],
    'books/edit' => ['BooksController', 'edit', ['id']],
    'musics/edit' => ['MusicsController', 'edit', ['id']],
    'videos/edit' => ['VideosController', 'edit', ['id']],
    'books/delete' => ['BooksController', 'delete', ['id']],
    'musics/delete' => ['MusicsController', 'delete', ['id']],
    'videos/delete' => ['VideosController', 'delete', ['id']],
    'profile/edit' => ['UserController', 'editProfile'],

    'borrowings/add' => ['BorrowingController', 'addBorrowing'],
    'borrowings/return/{id}' => ['BorrowingController', 'return', ['id']],];
