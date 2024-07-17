<?php

namespace App\Controller;

use App\Model\TypesManager;
use App\Model\VideosManager;
use App\Model\CategoriesManager;

class VideosController extends AbstractController
{
    /**
     * List Films
     */
    public function index(): string
    {
        $categoriesManager = new CategoriesManager();
        $typeManager = new TypesManager();
        $videosManager = new VideosManager();
        $medias = $videosManager->selectAll('title');

        foreach ($medias as &$media) {
            $media['categories'] = $categoriesManager->getCategoriesByVideoId($media['id']);
            $media['types'] = $typeManager->getTypesByVideoId($media['id']);
        }

        $title = "Films";
        $filters = ['Action', 'Comédie', 'Drame', 'Documentaire', 'Science-fiction', 'Horreur'];

        return $this->twig->render('Media/index.html.twig', [
            'page_title' => $title,
            'filters' => $filters,
            'medias' => $medias,
            'media_type' => 'videos'
        ]);
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
                $fileName = $this->uploadFile($errors);
                if ($fileName !== "") {
                    $media['picture'] = $fileName;
                } else {
                    $media['picture'] = null;
                }

                $videosManager->update($media);
                header('Location:/videos/show?id=' . $id);
                return null;
            }

            // Renvoyer le formulaire avec les erreurs et les données saisies
            return $this->twig->render('Media/edit.html.twig', ['categories' => $categories, 'types' => $types,
                'errors' => $errors, 'media' => $media, 'media_type' => 'videos', 'isEdit' => true]);
        }

        return $this->twig->render('Media/edit.html.twig', ['categories' => $categories, 'types' => $types,
            'media' => $media, 'media_type' => 'videos', 'isEdit' => true]);
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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Nettoyer les données POST
            $media = array_map('trim', $_POST);

            $errors = $this->validate($media);

            // Validation de la catégorie
            if (empty($media['id_type'])) {
                $errors['id_type'] = 'Le type est requis.';
            } elseif (!is_numeric($media['id_type'])) {
                $errors['id_type'] = 'Identifiant de type invalide.';
            }

            // Si aucune erreur, procéder à l'insertion
            if (empty($errors)) {
                $fileName = $this->uploadFile($errors);
                if ($fileName !== "") {
                    $media['picture'] = $fileName;
                } else {
                    $media['picture'] = null;
                }

                $videosManager = new VideosManager();
                $id = $videosManager->insert($media);
                header('Location:/videos/show?id=' . $id);
                return null;
            }

            // Renvoyer le formulaire avec les erreurs et les données saisies
            return $this->twig->render('Media/add.html.twig', ['categories' => $categories, 'types' => $types,
                'errors' => $errors, 'media' => $media, 'media_type' => 'videos']);
        }

        return $this->twig->render('Media/add.html.twig', ['categories' => $categories, 'types' => $types,
            'media_type' => 'videos']);
    }

    /**
     * Delete a specific item
     */
    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = trim($_POST['id']);
            $videosManager = new VideosManager();
            $video = $videosManager->selectOneById((int)$id);
            $fileName = "../public/assets/images/covers/" . $video['picture'];

            if ($video['picture'] && file_exists($fileName)) {
                unlink($fileName);  // Supprime le fichier image
            }

            $videosManager->delete((int)$id);
            header('Location:/books');
        }
    }
}
