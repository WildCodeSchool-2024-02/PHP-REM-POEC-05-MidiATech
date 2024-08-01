<?php

namespace App\Controller;

use App\Trait\MediasTrait;
use RuntimeException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class MusicsController extends AbstractController
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
            $categoryFullName = 'Music ' . $category;
            $medias = $this->managers->musicsManager->selectByCategory($categoryFullName);
        } else {
            $medias = $this->managers->musicsManager->selectAll();
        }

        foreach ($medias as &$media) {
            $media['categories'] = $this->managers->categoriesManager->getCategoriesByMusicId($media['id']);
        }

        $title = "Musiques";
        $filters = array_merge(['Tout'], $this->managers->categoriesManager->getAllMusicCategories());

        return $this->twig->render('Media/index.html.twig', [
            'page_title' => $title,
            'categoryFilters' => $filters,
            'typeFilters' => [],
            'medias' => $medias,
            'media_type' => 'musics',
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
        $media = $this->managers->musicsManager->selectOneById($id);

        return $this->twig->render('Media/show.html.twig', ['media' => $media, 'media_type' => 'musics']);
    }


    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function edit(int $id): ?string
    {
        $media = $this->managers->musicsManager->selectOneById($id);
        $categories = $this->managers->categoriesManager->selectAll();
        $userRole = $this->getUserRole();


        if ($userRole !== self::ADMIN) {
            $this->redirect('/musics');
            return null;
        }


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $media = array_map('trim', $_POST);

            $errors = $this->validate($media);

            // Si aucune erreur, procéder à l'insertion
            if (empty($errors)) {
                try {
                    $id = $this->managers->musicsManager->insert($media);
                    $this->redirect('/musics/show?id=' . $id);
                    return null;
                } catch (RunTimeException $e) {
                    return 'Error: ' . $e->getMessage();
                }
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
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function add(): ?string
    {
        $categories = $this->managers->categoriesManager->selectAll();
        $userRole = $this->getUserRole();


        if ($userRole !== self::ADMIN) {
            $this->redirect('/musics');
            return null;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $media = array_map('trim', $_POST);
            $errors = $this->validate($media);

            if (empty($errors)) {
                try {
                    $id = $this->managers->musicsManager->insert($media);
                    $this->redirect('/musics/show?id=' . $id);
                    return null;
                } catch (RunTimeException $e) {
                    return 'Error: ' . $e->getMessage();
                }
            }

            return $this->twig->render('Media/add.html.twig', [
                'categories' => $categories, 'errors' => $errors,
                'media' => $media, 'media_type' => 'musics'
            ]);
        }

        return $this->twig->render('Media/add.html.twig', ['categories' => $categories, 'media_type' => 'musics']);
    }

    public function delete(): void
    {
        $userRole = $this->getUserRole();

        if (($userRole === self::ADMIN) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = trim($_POST['id']);

            $this->managers->musicsManager->delete((int)$id);
            $this->redirect('/musics');
        }

        $this->redirect('/musics');
    }
}
