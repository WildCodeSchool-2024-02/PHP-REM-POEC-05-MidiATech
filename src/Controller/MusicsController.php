<?php

namespace App\Controller;

use App\Model\MusicsManager;
use App\Model\CategoriesManager;
use App\Services\FileUploadService;

class MusicsController extends AbstractController
{
    /**
     * List items
     */
    public function index(?string $category = null): string
    {
        $categoriesManager = new CategoriesManager();
        $musicsManager = new MusicsManager();

        if ($category && $category !== 'Tout') {
            $categoryFullName = 'Music ' . $category;
            $medias = $musicsManager->selectByCategory($categoryFullName);
        } else {
            $medias = $musicsManager->selectAll('title');
        }

        foreach ($medias as &$media) {
            $media['categories'] = $categoriesManager->getCategoriesByMusicId($media['id']);
        }

        $title = "Musics";
        $filters = array_merge(['Tout'], $categoriesManager->getAllMusicCategories());

        return $this->twig->render('Media/index.html.twig', [
            'page_title' => $title,
            'filters' => $filters,
            'medias' => $medias,
            'media_type' => 'musics',
            'selected_category' => $category
        ]);
    }


    /**
     * Show informations for a specific item
     */
    public function show(int $id): string
    {
        $musicsManager = new MusicsManager();
        $media = $musicsManager->selectOneById($id);

        return $this->twig->render('Media/show.html.twig', ['media' => $media, 'media_type' => 'musics']);
    }

    /**
     * Edit a specific item
     */
    public function edit(int $id): ?string
    {
        $musicsManager = new MusicsManager();
        $media = $musicsManager->selectOneById($id);
        $categoriesManager = new CategoriesManager();
        $categories = $categoriesManager->selectAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $media = array_map('trim', $_POST);

            $errors = $this->validate($media);

            // Si aucune erreur, procéder à l'insertion
            if (empty($errors)) {
                $uploadService = new FileUploadService();
                $fileName = $uploadService->uploadFile($errors);
                if ($fileName !== "") {
                    $media['picture'] = $fileName;
                } else {
                    $media['picture'] = null;
                }

                $musicsManager->update($media);
                header('Location:/musics/show?id=' . $id);
                return null;
            }

            // Renvoyer le formulaire avec les erreurs et les données saisies
            return $this->twig->render('Media/edit.html.twig', [
                'categories' => $categories, 'errors' => $errors,
                'media' => $media, 'media_type' => 'musics', 'isEdit' => true
            ]);
        }

        return $this->twig->render('Media/edit.html.twig', [
            'categories' => $categories, 'media' => $media,
            'media_type' => 'musics', 'isEdit' => true
        ]);
    }

    /**
     * Add a new item
     */
    public function add(): ?string
    {
        $categoriesManager = new CategoriesManager();
        $categories = $categoriesManager->selectAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Nettoyer les données POST
            $media = array_map('trim', $_POST);

            $errors = $this->validate($media);

            // Si aucune erreur, procéder à l'insertion
            if (empty($errors)) {
                $uploadService = new FileUploadService();
                $fileName = $uploadService->uploadFile($errors);
                if ($fileName !== "") {
                    $media['picture'] = $fileName;
                } else {
                    $media['picture'] = null;
                }

                $musicsManager = new MusicsManager();
                $id = $musicsManager->insert($media);
                header('Location:/musics/show?id=' . $id);
                return null;
            }

            // Renvoyer le formulaire avec les erreurs et les données saisies
            return $this->twig->render('Media/add.html.twig', [
                'categories' => $categories, 'errors' => $errors,
                'media' => $media, 'media_type' => 'musics'
            ]);
        }

        return $this->twig->render('Media/add.html.twig', ['categories' => $categories, 'media_type' => 'musics']);
    }

    /**
     * Delete a specific item
     */
    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = trim($_POST['id']);
            $musicsManager = new MusicsManager();
            $music = $musicsManager->selectOneById((int)$id);
            $fileName = "../public/assets/images/covers/" . $music['picture'];

            if ($music['picture'] && file_exists($fileName)) {
                unlink($fileName);  // Supprime le fichier image
            }

            $musicsManager->delete((int)$id);
            header('Location:/musics');
        }
    }
}
