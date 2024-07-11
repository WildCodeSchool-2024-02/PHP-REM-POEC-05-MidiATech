<?php

namespace App\Controller;

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
        $videosManager = new VideosManager();
        $medias = $videosManager->selectAll('title');

        foreach ($medias as &$media) {
            $media['categories'] = $categoriesManager->getCategoriesByVideoId($media['id']);
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

        return $this->twig->render('Media/showFilm.html.twig', compact('media'));
    }

    /**
     * Edit a specific item
     */
    public function edit(int $id): ?string
    {
        $videosManager = new VideosManager();
        $media = $videosManager->selectOneById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $media = array_map('trim', $_POST);

            // TODO validations (length, format...)

            // if validation is ok, update and redirection
            $videosManager->update($media);

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
            $videosManager = new VideosManager();
            $id = $videosManager->insert($media);

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
            $videosManager = new VideosManager();
            $videosManager->delete((int)$id);

            header('Location:/medias');
        }
    }
}
