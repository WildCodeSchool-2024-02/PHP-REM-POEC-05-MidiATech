<?php

namespace App\Controller;

use App\Model\BooksManager;

class AdminBooksController extends AdminController
{
    public function categories(): string
    {
        $book = $this->managers->booksManager->selectCategories();


        return $this->twig->render('Admin/categories.html.twig', [
            'media_type' => 'books',
            'media_fr' => 'livre',
            'categories_books' => $book
        ]);
    }
}
