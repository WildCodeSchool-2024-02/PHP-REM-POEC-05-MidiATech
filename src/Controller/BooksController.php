<?php

namespace App\Controller;

use App\Trait\MediasTrait;
use RuntimeException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use App\Model\AdminManager;

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
            $categoryFullName = 'books ' . $category;
            $medias = $this->managers->booksManager->selectByCategory($categoryFullName);
        } else {
            $medias = $this->managers->booksManager->selectAll();
        }

        foreach ($medias as &$media) {
            $media['categories'] = $this->managers->categoriesManager->getCategoriesByBookId($media['id']);
        }

        $title = "Livres";
        $categories = array_map(static function ($category) {
            return $category['name'];
        }, $this->managers->categoriesManager->getAllBookCategories());
        $filters = array_merge(['Tout'], $categories);

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
    public function show(int $id): string
    {
        $media = $this->managers->booksManager->selectOneById($id);

        return $this->twig->render('Media/show.html.twig', [
            'media' => $media,
            'media_type' => 'books'
        ]);
    }


    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function edit(int $id): ?string
    {
        $media = $this->managers->booksManager->selectOneById($id);
        $categories = $this->managers->categoriesManager->getAllBookCategories();
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
                    $id = $this->managers->booksManager->update($media);
                    $this->redirect('/books/show?id=' . $id);
                    return null;
                } catch (RunTimeException $e) {
                    return 'Error: ' . $e->getMessage();
                }
            }

            // Renvoyer le formulaire avec les erreurs et les données saisies
            return $this->twig->render('Media/edit.html.twig', [
                'categories' => $categories, 'errors' => $errors,
                'media' => $media,
                'media_type' => 'books',
                'isEdit' => true
            ]);
        }

        return $this->twig->render('Media/edit.html.twig', [
            'categories' => $categories,
            'media' => $media,
            'media_type' => 'books',
            'isEdit' => true
        ]);
    }


    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function add(): ?string
    {
        $categories = $this->managers->categoriesManager->getAllBookCategories();
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
                'categories' => $categories,
                'errors' => $errors,
                'media' => $media,
                'media_type' => 'books'
            ]);
        }

        return $this->twig->render('Media/add.html.twig', [
            'categories' => $categories,
            'media_type' => 'books'
        ]);
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

    public function deleteCategories(): void
    {
        $adminManager = new AdminManager();
        $userRole = $this->getUserRole();

        if (($userRole === self::ADMIN) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = trim($_POST['id']);

            $adminManager->deleteCategories((int)$id);
            $this->redirect('/admin/categories/books');
        }

        $this->redirect('/admin/categories/books');
    }

    public function editCategories(int $id): ?string
    {
        $adminManager = new AdminManager();
        $categorie = $adminManager->selectCategoriesById($id);
        $userRole = $this->getUserRole();

        if ($userRole !== self::ADMIN) {
            $this->redirect('/');
            return null;
        }


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $categorie = array_map('trim', $_POST);

            $errors = $this->validateCategories($categorie);

            // Si aucune erreur, procéder à l'insertion
            if (empty($errors)) {
                try {
                    $adminManager->updateCategories($categorie);
                    $this->redirect('/admin/categories/books');
                    return null;
                } catch (RunTimeException $e) {
                    return 'Error: ' . $e->getMessage();
                }
            }

            // Renvoyer le formulaire avec les erreurs et les données saisies
            return $this->twig->render('Admin/edit.html.twig', [
                'media_type' => 'books',
                'isEdit' => true,
                'categorie' => $categorie,
                'errors' => $errors
            ]);
        }

        return $this->twig->render('Admin/edit.html.twig', [
            'media_type' => 'books',
            'isEdit' => true,
            'categorie' => $categorie
        ]);
    }

    public function addCategories(): ?string
    {
        $adminManager = new AdminManager();
        $userRole = $this->getUserRole();

        if ($userRole !== self::ADMIN) {
            $this->redirect('/books');
            return null;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categorie = array_map('trim', $_POST);
            $errors = $this->validateCategories($categorie);

            if (empty($errors)) {
                try {
                    $adminManager->insertCategories($categorie);
                    $this->redirect('/admin/categories/books');
                    return null;
                } catch (RunTimeException $e) {
                    return 'Error: ' . $e->getMessage();
                }
            }

            return $this->twig->render('Admin/add.html.twig', [
                'categorie' => $categorie,
                'errors' => $errors,
                'media_type' => 'books'
            ]);
        }

        return $this->twig->render('Admin/add.html.twig', [
            'media_type' => 'books',
            'categorie' => true
        ]);
    }
}
