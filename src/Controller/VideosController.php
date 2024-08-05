<?php

namespace App\Controller;

use App\Trait\MediasTrait;
use RuntimeException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use App\Model\AdminManager;

class VideosController extends AbstractController
{
    use MediasTrait;


    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function index(?string $category = null, ?string $type = null): string
    {
        $medias = $this->getMedias($this->managers->videosManager, $category, $type);

        foreach ($medias as &$media) {
            $media['categories'] = $this->managers->categoriesManager->getCategoriesByVideoId($media['id']);
        }

        $this->addCategoriesAndTypes($medias, $this->managers->categoriesManager, $this->managers->typesManager);

        $title = "Films - Séries - Jeunesses - Documentaires";

        $typeFilters = array_merge(['Tout'], $this->managers->typesManager->getAllTypes());

        $categories = array_map(static function ($category) {
            return $category['name'];
        }, $this->managers->categoriesManager->getAllVideoCategories());
        $filters = array_merge(['Tout'], $categories);

        return $this->twig->render('Media/index.html.twig', [
            'page_title' => $title,
            'categoryFilters' => $filters,
            'typeFilters' => $typeFilters,
            'medias' => $medias,
            'media_type' => 'videos',
            'selected_category' => $category,
            'selected_type' => null
        ]);
    }

    private function getMedias($videosManager, ?string $category, ?string $type): array
    {
        if ($this->hasCategoryAndType($category, $type)) {
            return $videosManager->selectByCategoryAndType($category, $type);
        }

        if ($this->hasCategory($category)) {
            $categoryFullName = 'videos ' . $category;
            return $videosManager->selectByCategory($categoryFullName);
        }

        if ($this->hasType($type)) {
            return $videosManager->selectByType($type);
        }

        return $videosManager->selectAll('title');
    }


    private function addCategoriesAndTypes(
        array &$medias,
        $categoriesManager,
        $typeManager
    ): void {
        foreach ($medias as &$media) {
            $media['categories'] = $categoriesManager->getCategoriesByVideoId($media['id']);
            $media['types'] = $typeManager->getTypesByVideoId($media['id']);
        }
    }

    private function hasCategoryAndType(?string $category, ?string $type): bool
    {
        return $category && $category !== 'Tout' && $type && $type !== 'Tout';
    }

    private function hasCategory(?string $category): bool
    {
        return $category && $category !== 'Tout';
    }

    private function hasType(?string $type): bool
    {
        return $type && $type !== 'Tout';
    }


    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function show(int $id): string
    {
        $media = $this->managers->videosManager->selectOneById($id);

        return $this->twig->render('Media/show.html.twig', ['media' => $media, 'media_type' => 'videos']);
    }


    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function edit(int $id): ?string
    {
        $media = $this->managers->videosManager->selectOneById($id);
        $categories = $this->managers->categoriesManager->getAllVideoCategories();
        $types = $this->managers->typesManager->selectAll();
        $userRole = $this->getUserRole();


        if ($userRole !== self::ADMIN) {
            $this->redirect('/musics');
            return null;
        }


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $media = array_map('trim', $_POST);

            $errors = $this->validate($media);

            // Validation de la catégorie
            if (empty($media['id_types'])) {
                $errors['id_types'] = 'Le type est requis.';
            } elseif (!is_numeric($media['id_types'])) {
                $errors['id_types'] = 'Identifiant de type invalide.';
            }

            // Si aucune erreur, procéder à l'insertion
            if (empty($errors)) {
                try {
                    $this->managers->videosManager->update($media);
                    $this->redirect('/videos/show?id=' . $id);
                    return null;
                } catch (RunTimeException $e) {
                    return 'Error: ' . $e->getMessage();
                }
            }

            // Renvoyer le formulaire avec les erreurs et les données saisies
            return $this->twig->render('Media/edit.html.twig', [
                'categories' => $categories,
                'types' => $types,
                'errors' => $errors,
                'media' => $media,
                'media_type' => 'videos',
                'isEdit' => true
            ]);
        }

        return $this->twig->render('Media/edit.html.twig', [
            'categories' => $categories,
            'types' => $types,
            'media' => $media,
            'media_type' => 'videos',
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
        $categories = $this->managers->categoriesManager->getAllVideoCategories();
        $types = $this->managers->typesManager->selectAll();
        $userRole = $this->getUserRole();


        if ($userRole !== self::ADMIN) {
            $this->redirect('/videos');
            return null;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $media = array_map('trim', $_POST);
            $errors = $this->validate($media);

            // Validation de la catégorie
            if (empty($media['id_types'])) {
                $errors['id_types'] = 'Le type est requis.';
            } elseif (!is_numeric($media['id_types'])) {
                $errors['id_types'] = 'Identifiant de type invalide.';
            }

            if (empty($errors)) {
                try {
                    $id = $this->managers->videosManager->insert($media);
                    $this->redirect('/videos/show?id=' . $id);
                    return null;
                } catch (RunTimeException $e) {
                    return 'Error: ' . $e->getMessage();
                }
            }

            return $this->twig->render('Media/add.html.twig', [
                'categories' => $categories, 'types' => $types,
                'errors' => $errors, 'media' => $media, 'media_type' => 'videos'
            ]);
        }

        return $this->twig->render('Media/add.html.twig', [
            'categories' => $categories, 'types' => $types,
            'media_type' => 'videos'
        ]);
    }

    /**
     * Delete a specific item
     */
    public function delete(): void
    {
        $userRole = $this->getUserRole();

        if (($userRole === self::ADMIN) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = trim($_POST['id']);

            $this->managers->videosManager->delete((int)$id);
            $this->redirect('/videos');
        }

        $this->redirect('/videos');
    }

    public function deleteCategories(): void
    {
        $adminManager = new AdminManager();
        $userRole = $this->getUserRole();

        if (($userRole === self::ADMIN) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = trim($_POST['id']);

            $adminManager->deleteCategories((int)$id);
            $this->redirect('/admin/categories/videos');
        }

        $this->redirect('/admin/categories/videos');
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
                    $this->redirect('/admin/categories/videos');
                    return null;
                } catch (RunTimeException $e) {
                    return 'Error: ' . $e->getMessage();
                }
            }

            // Renvoyer le formulaire avec les erreurs et les données saisies
            return $this->twig->render('Admin/edit.html.twig', [
                'media_type' => 'videos',
                'isEdit' => true,
                'categorie' => $categorie,
                'errors' => $errors
            ]);
        }

        return $this->twig->render('Admin/edit.html.twig', [
            'media_type' => 'videos',
            'isEdit' => true,
            'categorie' => $categorie
        ]);
    }

    public function addCategories(): ?string
    {
        $adminManager = new AdminManager();
        $userRole = $this->getUserRole();

        if ($userRole !== self::ADMIN) {
            $this->redirect('/videos');
            return null;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categorie = array_map('trim', $_POST);
            $errors = $this->validateCategories($categorie);

            if (empty($errors)) {
                try {
                    $adminManager->insertCategories($categorie);
                    $this->redirect('/admin/categories/videos');
                    return null;
                } catch (RunTimeException $e) {
                    return 'Error: ' . $e->getMessage();
                }
            }

            return $this->twig->render('Admin/add.html.twig', [
                'categorie' => $categorie,
                'errors' => $errors,
                'media_type' => 'videos'
            ]);
        }

        return $this->twig->render('Admin/add.html.twig', [
            'media_type' => 'videos',
            'categorie' => true
        ]);
    }
}
