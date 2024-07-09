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
        $videos = $videosManager->selectAll('title');

        return $this->twig->render('Item.dist/index.html.twig', compact('videos'));
    }

    /**
     * Show informations for a specific item
     */
    public function show(int $id): string
    {
        $videosManager = new VideosManager();
        $video = $videosManager->selectOneById($id);

        return $this->twig->render('Video/show.html.twig', compact('video'));
    }

    /**
     * Edit a specific item
     */
    public function edit(int $id): ?string
    {
        $videosManager = new VideosManager();
        $video = $videosManager->selectOneById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $video = array_map('trim', $_POST);

            // TODO validations (length, format...)

            // if validation is ok, update and redirection
            $videosManager->update($video);

            header('Location: /videos/show?id=' . $id);

            // we are redirecting so we don't want any content rendered
            return null;
        }

        return $this->twig->render('Video/edit.html.twig', compact('video'));
    }

    /**
     * Add a new item
     */
    public function add(): ?string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $video = array_map('trim', $_POST);

            // TODO validations (length, format...)

            // if validation is ok, insert and redirection
            $videosManager = new VideosManager();
            $id = $videosManager->insert($video);

            header('Location:/videos/show?id=' . $id);
            return null;
        }

        return $this->twig->render('Video/add.html.twig');
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

            header('Location:/videos');
        }
    }
}
