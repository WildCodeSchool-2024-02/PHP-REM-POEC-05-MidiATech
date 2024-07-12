<?php

namespace App\Controller;

use App\Model\BooksManager;
use App\Model\CategoriesManager;

class BooksController extends AbstractController
{
    /**
     * List Books
     */
    public function index(): string
    {
        $categoriesManager = new CategoriesManager();

        $booksManager = new BooksManager();
        $medias = $booksManager->selectAll('title');

        foreach ($medias as &$media) {
            $media['categories'] = $categoriesManager->getCategoriesByBookId($media['id']);
        }

        $title = "Livres";
        $filters = ['Roman', 'Policier', 'Science-fiction', 'Fantastique', 'Histoire', 'Essai'];

        return $this->twig->render('Media/index.html.twig', [
            'page_title' => $title,
            'filters' => $filters,
            'medias' => $medias,
            'media_type' => 'books'

        ]);
    }

    /**
     * Show informations for a specific book
     */
    public function show(int $id): string
    {
        $booksManager = new BooksManager();
        $media = $booksManager->selectOneById($id);

        return $this->twig->render('Media/showBook.html.twig', ['media' => $media]);
    }
    /**
     * Edit a specific book
     */
    public function edit(int $id): ?string
    {
        $booksManager = new BooksManager();
        $media = $booksManager->selectOneById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $media = array_map('trim', $_POST);

            // TODO validations (length, format...)

            // if validation is ok, update and redirection
            $booksManager->update($media);

            header('Location: /medias/show?id=' . $id);

            // we are redirecting so we don't want any content rendered
            return null;
        }

        return $this->twig->render('Media/edit.html.twig', compact('media'));
    }

    /**
     * Add a new item
     */
    public function add(): ?string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $media = array_map('trim', $_POST);

            // TODO validations (length, format...)

            // if validation is ok, insert and redirection
            $booksManager = new BooksManager();
            $id = $booksManager->insert($media);

            header('Location:/medias/show?id=' . $id);
            return null;
        }

        return $this->twig->render('Media/add.html.twig');
    }

    /**
     * Delete a specific item
     */
    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = trim($_POST['id']);
            $booksManager = new BooksManager();
            $booksManager->delete((int)$id);

            header('Location:/medias');
        }
    }
}
