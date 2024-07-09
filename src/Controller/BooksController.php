<?php

namespace App\Controller;

use App\Model\BooksManager;

class BooksController extends AbstractController
{
    /**
     * List items
     */
    public function index(): string
    {
        $booksManager = new BooksManager();
        $books = $booksManager->selectAll('title');

        return $this->twig->render('Book/index.html.twig', compact('books'));
    }

    /**
     * Show informations for a specific item
     */
    public function show(int $id): string
    {
        $booksManager = new BooksManager();
        $book = $booksManager->selectOneById($id);

        return $this->twig->render('Book/show.html.twig', compact('book'));
    }

    /**
     * Edit a specific item
     */
    public function edit(int $id): ?string
    {
        $booksManager = new BooksManager();
        $book = $booksManager->selectOneById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $book = array_map('trim', $_POST);

            // TODO validations (length, format...)

            // if validation is ok, update and redirection
            $booksManager->update($book);

            header('Location: /books/show?id=' . $id);

            // we are redirecting so we don't want any content rendered
            return null;
        }

        return $this->twig->render('Book/edit.html.twig', compact('book'));
    }

    /**
     * Add a new item
     */
    public function add(): ?string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $book = array_map('trim', $_POST);

            // TODO validations (length, format...)

            // if validation is ok, insert and redirection
            $booksManager = new BooksManager();
            $id = $booksManager->insert($book);

            header('Location:/books/show?id=' . $id);
            return null;
        }

        return $this->twig->render('Book/add.html.twig');
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

            header('Location:/books');
        }
    }
}
