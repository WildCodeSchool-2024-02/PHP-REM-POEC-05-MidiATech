<?php

namespace App\Controller;

use App\Trait\MediasTrait;
use RuntimeException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class BooksController extends AbstractController
{
    use MediasTrait;

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function index(?string $category = null): string
    {
        if ($category && $category !== 'Tout') {
            $categoryFullName = 'Book ' . $category;
            $medias = $this->managers->booksManager->selectByCategory($categoryFullName);
        } else {
            $medias = $this->managers->booksManager->selectAll();
        }

        foreach ($medias as &$media) {
            $media['categories'] = $this->managers->categoriesManager->getCategoriesByBookId($media['id']);
        }

        $title = "Livres";
        $filters = array_merge(['Tout'], $this->managers->categoriesManager->getAllBookCategories());

        return $this->twig->render('Media/index.html.twig', [
            'page_title' => $title,
            'categoryFilters' => $filters,
            'typeFilters' => [],
            'medias' => $medias,
            'media_type' => 'books',
            'selected_category' => $category,
            'selected_type' => null
        ]);
    }


    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function show($id): string
    {
        $media = $this->managers->booksManager->selectOneById($id);

        return $this->twig->render('Media/show.html.twig', ['media' => $media, 'media_type' => 'books']);
    }


    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function edit(int $id): ?string
    {
        $media = $this->managers->booksManager->selectOneById($id);
        $categories = $this->managers->categoriesManager->selectAll();
        $userRole = $this->getUserRole();

        if ($userRole !== self::ADMIN) {
            $this->redirect('/books');
            return null;
        }


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $media = array_map('trim', $_POST);

            $errors = $this->validate($media);

            // Si aucune erreur, procéder à l'insertion
            if (empty($errors)) {
                try {
                    $id = $this->managers->booksManager->insert($media);
                    $this->redirect('/books/show?id=' . $id);
                    return null;
                } catch (RunTimeException $e) {
                    return 'Error: ' . $e->getMessage();
                }
            }

            // Renvoyer le formulaire avec les erreurs et les données saisies
            return $this->twig->render('Media/edit.html.twig', [
                'categories' => $categories, 'errors' => $errors,
                'media' => $media, 'media_type' => 'books', 'isEdit' => true
            ]);
        }

        return $this->twig->render('Media/edit.html.twig', [
            'categories' => $categories, 'media' => $media,
            'media_type' => 'books', 'isEdit' => true
        ]);
    }


    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function add(): ?string
    {
        $categories = $this->managers->categoriesManager->selectAll();
        $userRole = $this->getUserRole();

        if ($userRole !== self::ADMIN) {
            $this->redirect('/books');
            return null;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $media = array_map('trim', $_POST);
            $errors = $this->validate($media);

            if (empty($errors)) {
                try {
                    $id = $this->managers->booksManager->insert($media);
                    $this->redirect('/books/show?id=' . $id);
                    return null;
                } catch (RunTimeException $e) {
                    return 'Error: ' . $e->getMessage();
                }
            }

            return $this->twig->render('Media/add.html.twig', [
                'categories' => $categories, 'errors' => $errors,
                'media' => $media, 'media_type' => 'books'
            ]);
        }

        return $this->twig->render('Media/add.html.twig', ['categories' => $categories, 'media_type' => 'books']);
    }

    public function delete(): void
    {
        $userRole = $this->getUserRole();

        if (($userRole === self::ADMIN) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = trim($_POST['id']);

            $this->managers->booksManager->delete((int)$id);
            $this->redirect('/books');
        }

        $this->redirect('/books');
    }
}
