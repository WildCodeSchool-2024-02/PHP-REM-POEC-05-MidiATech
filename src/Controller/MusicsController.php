<?php

namespace App\Controller;

use App\Model\MusicsManager;

class MusicsController extends AbstractController
{
    /**
     * List items
     */
    public function index(): string
    {
        $musicsManager = new MusicsManager();
        $musics = $musicsManager->selectAll('title');

        return $this->twig->render('Music/index.html.twig', compact('musics'));
    }

    /**
     * Show informations for a specific item
     */
    public function show(int $id): string
    {
        $musicsManager = new MusicsManager();
        $music = $musicsManager->selectOneById($id);

        return $this->twig->render('Music/show.html.twig', compact('music'));
    }

    /**
     * Edit a specific item
     */
    public function edit(int $id): ?string
    {
        $musicsManager = new MusicsManager();
        $music = $musicsManager->selectOneById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $music = array_map('trim', $_POST);

            // TODO validations (length, format...)

            // if validation is ok, update and redirection
            $musicsManager->update($music);

            header('Location: /music/show?id=' . $id);

            // we are redirecting, so we don't want any content rendered
            return null;
        }

        return $this->twig->render('Music/edit.html.twig', compact('music'));
    }

    /**
     * Add a new item
     */
    public function add(): ?string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $music = array_map('trim', $_POST);

            // TODO validations (length, format...)

            // if validation is ok, insert and redirection
            $musicsManager = new MusicsManager();
            $id = $musicsManager->insert($music);

            header('Location:/musics/show?id=' . $id);
            return null;
        }

        return $this->twig->render('Music/add.html.twig');
    }

    /**
     * Delete a specific item
     */
    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = trim($_POST['id']);
            $musicsManager = new MusicsManager();
            $musicsManager->delete((int)$id);

            header('Location:/musics');
        }
    }
}
