<?php

namespace App\Controller;

use App\Model\BooksManager;
use App\Model\CategoriesManager;
use App\Trait\MediasTrait;

class BooksController extends AbstractController
{
    use MediasTrait;

    /**
     * List Books
     */
    public function index(?string $category = null): string
    {
        $categoriesManager = new CategoriesManager();
        $booksManager = new BooksManager();

        if ($category && $category !== 'Tout') {
            $categoryFullName = 'Book ' . $category;
            $medias = $booksManager->selectByCategory($categoryFullName);
        } else {
            $medias = $booksManager->selectAll('title');
        }

        foreach ($medias as &$media) {
            $media['categories'] = $categoriesManager->getCategoriesByBookId($media['id']);
        }

        $title = "Livres";
        $filters = array_merge(['Tout'], $categoriesManager->getAllBookCategories());

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
     * Show informations for a specific book
     */
    public function show($id): string
    {
        $id = (int) $id;
        $booksManager = new BooksManager();
        $media = $booksManager->selectOneById($id);

        return $this->twig->render('Media/show.html.twig', ['media' => $media, 'media_type' => 'books']);
    }

    /**
     * Edit a specific book
     */
    public function edit(int $id): ?string
    {
        $booksManager = new BooksManager();
        $media = $booksManager->selectOneById($id);
        $categoriesManager = new CategoriesManager();
        $categories = $categoriesManager->selectAll();
        $userRole = $this->getUserRole();

        if ($userRole === 'admin') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // clean $_POST data
                $media = array_map('trim', $_POST);

                $errors = $this->validate($media);

                // Si aucune erreur, procéder à l'insertion
                if (empty($errors)) {
                    $booksManager->update($media);
                    header('Location:/books/show?id=' . $id);
                    return null;
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

        header('Location:/books');
        return null;
    }

    /**
     * Add a new item
     */
    public function add(): ?string
    {
        $categoriesManager = new CategoriesManager();
        $categories = $categoriesManager->selectAll();
        $userRole = $this->getUserRole();

        if ($userRole === 'admin') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Nettoyer les données POST
                $media = array_map('trim', $_POST);

                $errors = $this->validate($media);

                // Si aucune erreur, procéder à l'insertion
                if (empty($errors)) {
                    $booksManager = new BooksManager();
                    $id = $booksManager->insert($media);
                    header('Location:/books/show?id=' . $id);
                    return null;
                }

                // Renvoyer le formulaire avec les erreurs et les données saisies
                return $this->twig->render('Media/add.html.twig', [
                    'categories' => $categories, 'errors' => $errors,
                    'media' => $media, 'media_type' => 'books'
                ]);
            }

            return $this->twig->render('Media/add.html.twig', ['categories' => $categories, 'media_type' => 'books']);
        }

        header('Location:/books');
        return null;
    }

    /**
     * Delete a specific item
     */
    public function delete(): void
    {
        $userRole = $this->getUserRole();

        if (($userRole === 'admin') && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = trim($_POST['id']);
            $booksManager = new BooksManager();
            $book = $booksManager->selectOneById((int)$id);
            $fileName = "../public/assets/images/covers/" . $book['picture'];

            if ($book['picture'] && file_exists($fileName)) {
                unlink($fileName);  // Supprime le fichier image
            }

            $booksManager->delete((int)$id);
            header('Location:/books');
        }

        header('Location:/books');
    }
}
