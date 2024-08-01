<?php

namespace App\Controller;

class AdminBooksController extends AdminController
{
    public function categories(): string
    {
        return $this->twig->render('Admin/categories.html.twig', [
            'media_type' => 'books',
            'media_fr' => 'livre'
        ]);
    }

}