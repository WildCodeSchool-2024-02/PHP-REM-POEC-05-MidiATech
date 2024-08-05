<?php

// list of accessible routes of your application, add every new route here
// key : route to match
// values : 1. controller name
//          2. method name
//          3. (optional) array of query string keys to send as parameter to the method
// e.g route '/item/edit?id=1' will execute $itemController->edit(1)
return [
    '' => ['HomeController', 'index'],
    'books' => ['BooksController', 'index', ['category']],
    'musics' => ['MusicsController', 'index', ['category']],
    'videos' => ['VideosController', 'index', ['category', 'type']],
    'search' => ['SearchController', 'search'],

    'login' => ['ConnectController', 'login'],
    'register' => ['ConnectController', 'register'],
    'logout' => ['ConnectController', 'logout'],
    'admin' => ['AdminController', 'index'],
    'account' => ['UserController', 'index'],
    'account/edit' => ['UserController', 'editProfile'],

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

    'admin/reservations' => ['AdminController', 'reservations'],
    'admin/categories' => ['AdminController', 'categoriesMedias'],

    'admin/categories/books' => ['AdminBooksController', 'categories'],
    'admin/categories/books/delete' => ['BooksController', 'deleteCategories', ['id']],
    'admin/categories/books/edit' => ['BooksController', 'editCategories', ['id']],
    'admin/categories/books/add' => ['BooksController', 'addCategories'],

    'admin/categories/musics' => ['AdminMusicsController', 'categories'],
    'admin/categories/musics/delete' => ['MusicsController', 'deleteCategories', ['id']],
    'admin/categories/musics/edit' => ['MusicsController', 'editCategories', ['id']],
    'admin/categories/musics/add' => ['MusicsController', 'addCategories'],

    'admin/categories/videos' => ['AdminVideosController', 'categories'],
    'admin/categories/videos/delete' => ['VideosController', 'deleteCategories', ['id']],
    'admin/categories/videos/edit' => ['VideosController', 'editCategories', ['id']],
    'admin/categories/videos/add' => ['VideosController', 'addCategories'],

    'admin/types/videos/delete' => ['VideosTypesController', 'deleteTypes', ['id']],
    'admin/types/videos/edit' => ['VideosTypesController', 'editTypes', ['id']],
    'admin/types/videos/add' => ['VideosTypesController', 'addTypes'],

    'admin/delete-reservation' => ['AdminController', 'deleteReservation'],
    'admin/stocks' => ['AdminController', 'stocks'],
    'profile/edit' => ['UserController', 'editProfile'],
    'borrowings/add' => ['BorrowingController', 'addBorrowing'],
    'borrowings/retour' => ['BorrowingController', 'retour', ['id']],
    'borrowings/request-return' => ['BorrowingController', 'requestReturn', ['id']],
    'admin/accept-return' => ['AdminController', 'acceptReturn', ['id']],
    'admin/update-stock' => ['AdminController', 'handleUpdateStock'],
];
