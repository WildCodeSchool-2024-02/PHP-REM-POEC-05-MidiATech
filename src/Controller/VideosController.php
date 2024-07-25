<?php

namespace App\Controller;

use App\Model\TypesManager;
use App\Model\VideosManager;
use App\Model\CategoriesManager;
use App\Trait\MediasTrait;

class VideosController extends AbstractController
{
    use MediasTrait;

    /**
     * List Films
     */
    public function index(?string $category = null, ?string $type = null): string
    {
        $categoriesManager = new CategoriesManager();
        $typeManager = new TypesManager();
        $videosManager = new VideosManager();

        $medias = $this->getMedias($videosManager, $category, $type);

        $this->addCategoriesAndTypes($medias, $categoriesManager, $typeManager);

        $title = "Films - Séries - Jeunesses - Documentaires";
        $categoryFilters = array_merge(['Tout'], $categoriesManager->getAllVideoCategories());
        $typeFilters = array_merge(['Tout'], $typeManager->getAllTypes());

        return $this->twig->render('Media/index.html.twig', [
            'page_title' => $title,
            'categoryFilters' => $categoryFilters,
            'typeFilters' => $typeFilters,
            'medias' => $medias,
            'media_type' => 'videos',
            'selected_category' => $category,
            'selected_type' => $type
        ]);
    }

    private function getMedias(VideosManager $videosManager, ?string $category, ?string $type): array
    {
        if ($this->hasCategoryAndType($category, $type)) {
            $categoryFullName = 'Video ' . $category;
            return $videosManager->selectByCategoryAndType($categoryFullName, $type);
        }

        if ($this->hasCategory($category)) {
            $categoryFullName = 'Video ' . $category;
            return $videosManager->selectByCategory($categoryFullName);
        }

        if ($this->hasType($type)) {
            return $videosManager->selectByType($type);
        }

        return $videosManager->selectAll('title');
    }

    private function addCategoriesAndTypes(
        array &$medias,
        CategoriesManager $categoriesManager,
        TypesManager $typeManager
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
     * Show informations for a specific item
     */
    public function show(int $id): string
    {
        $videosManager = new VideosManager();
        $media = $videosManager->selectOneById($id);

        return $this->twig->render('Media/show.html.twig', ['media' => $media, 'media_type' => 'videos']);
    }

    /**
     * Edit a specific item
     */
    public function edit(int $id): ?string
    {
        $videosManager = new VideosManager();
        $media = $videosManager->selectOneById($id);
        $categoriesManager = new CategoriesManager();
        $categories = $categoriesManager->selectAll();
        $typeManager = new TypesManager();
        $types = $typeManager->selectAll();
        $userRole = $this->getUserRole();

        if ($userRole === 'admin') {
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
                    $videosManager->update($media);
                    header('Location:/videos/show?id=' . $id);
                    return null;
                }

                // Renvoyer le formulaire avec les erreurs et les données saisies
                return $this->twig->render('Media/edit.html.twig', [
                    'categories' => $categories, 'types' => $types,
                    'errors' => $errors, 'media' => $media, 'media_type' => 'videos', 'isEdit' => true
                ]);
            }

            return $this->twig->render('Media/edit.html.twig', [
                'categories' => $categories, 'types' => $types,
                'media' => $media, 'media_type' => 'videos', 'isEdit' => true
            ]);
        }

        header('Location:/videos');
        return null;
    }

    /**
     * Add a new item
     */
    public function add(): ?string
    {
        $categoriesManager = new CategoriesManager();
        $categories = $categoriesManager->selectAll();
        $typeManager = new TypesManager();
        $types = $typeManager->selectAll();
        $userRole = $this->getUserRole();

        if ($userRole === 'admin') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Nettoyer les données POST
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
                    $videosManager = new VideosManager();
                    $id = $videosManager->insert($media);
                    header('Location:/videos/show?id=' . $id);
                    return null;
                }

                // Renvoyer le formulaire avec les erreurs et les données saisies
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

        header('Location:/videos');
        return null;
    }

    /**
     * Delete a specific item
     */
    public function delete(): void
    {
        $userRole = $this->getUserRole();

        if ($userRole === 'admin') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $id = trim($_POST['id']);
                $videosManager = new VideosManager();
                $video = $videosManager->selectOneById((int)$id);
                $fileName = "../public/assets/images/covers/" . $video['picture'];

                if ($video['picture'] && file_exists($fileName)) {
                    unlink($fileName);  // Supprime le fichier image
                }

                $videosManager->delete((int)$id);
                header('Location:/videos');
            }
        }

        header('Location:/videos');
    }
}
