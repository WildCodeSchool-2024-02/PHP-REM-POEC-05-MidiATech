<?php

namespace App\Controller;

use App\Model\VideosManager;

class VideosController extends AbstractController
{
    /**
     * List items
     */
    public function index(): string
    {
        $videosManager = new VideosManager();
        $medias = $videosManager->selectAll('title');

        return $this->twig->render('Media/index.html.twig', compact('medias'));
    }

    /**
     * Show informations for a specific item
     */
    public function show(int $id): string
    {
        $videosManager = new VideosManager();
        $media = $videosManager->selectOneById($id);

        return $this->twig->render('Media/show.html.twig', compact('media'));
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
