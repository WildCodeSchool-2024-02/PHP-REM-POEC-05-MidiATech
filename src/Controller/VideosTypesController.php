<?php

namespace App\Controller;

use RuntimeException;
use App\Model\AdminManager;

class VideosTypesController extends VideosController
{
    public function deleteTypes(): void
    {
        $adminManager = new AdminManager();
        $userRole = $this->getUserRole();

        if (($userRole === self::ADMIN) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = trim($_POST['id']);

            $adminManager->deleteTypes((int)$id);
            $this->redirect('/admin/categories/videos');
        }

        $this->redirect('/admin/categories/videos');
    }

    public function editTypes(int $id): ?string
    {
        $adminManager = new AdminManager();
        $type = $adminManager->selectTypesById($id);
        $userRole = $this->getUserRole();

        if ($userRole !== self::ADMIN) {
            $this->redirect('/');
            return null;
        }


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $type = array_map('trim', $_POST);

            $errors = $this->validateCategories($type);

            // Si aucune erreur, procéder à l'insertion
            if (empty($errors)) {
                try {
                    $adminManager->updateTypes($type);
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
                'type' => $type,
                'errors' => $errors
            ]);
        }

        return $this->twig->render('Admin/edit.html.twig', [
            'media_type' => 'videos',
            'isEdit' => true,
            'type' => $type
        ]);
    }

    public function addTypes(): ?string
    {
        $adminManager = new AdminManager();
        $userRole = $this->getUserRole();

        if ($userRole !== self::ADMIN) {
            $this->redirect('/videos');
            return null;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $type = array_map('trim', $_POST);
            $errors = $this->validateCategories($type);

            if (empty($errors)) {
                try {
                    $adminManager->insertTypes($type);
                    $this->redirect('/admin/categories/videos');
                    return null;
                } catch (RunTimeException $e) {
                    return 'Error: ' . $e->getMessage();
                }
            }

            return $this->twig->render('Admin/add.html.twig', [
                'type' => $type,
                'errors' => $errors,
                'media_type' => 'videos'
            ]);
        }

        return $this->twig->render('Admin/add.html.twig', [
            'media_type' => 'videos',
            'type' => true
        ]);
    }
}
