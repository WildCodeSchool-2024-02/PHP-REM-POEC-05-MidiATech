<?php

namespace App\Controller;

use Exception;
use Ramsey\Uuid\Uuid;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use App\Model\UserManager;

/**
 * Initialized some Controller common features (Twig...)
 */
abstract class AbstractController
{
    protected Environment $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader(APP_VIEW_PATH);
        $this->twig = new Environment(
            $loader,
            [
                'cache' => false,
                'debug' => true,
            ]
        );
        $this->twig->addExtension(new DebugExtension());
        $this->twig->addGlobal('app', ['user' => $this->getUser()]);
    }

    private function getUser()
    {
        if (isset($_SESSION['user_id'])) {
            $userManager = new UserManager();
            return $userManager->selectOneById($_SESSION['user_id']);
        } elseif (isset($_COOKIE['user_id'])) {
            $userManager = new UserManager();
            return $userManager->selectOneById($_COOKIE['user_id']);
        }
        return null;
    }

    public function validate($media): array
    {
        // Définir les erreurs
        $errors = [];

        // Validation de l'image
        if (isset($_FILES['picture'])) {
            if (
                ($_FILES['picture']['error'] !== UPLOAD_ERR_OK)
                && ($_FILES['picture']['error'] !== UPLOAD_ERR_NO_FILE)
            ) {
                $errors['picture'] = "Impossible d'uploader l'image";
            }
        }

        // Validation du titre
        if (empty($media['title'])) {
            $errors['title'] = 'Le titre est requis.';
        } elseif (strlen($media['title']) > 255) {
            $errors['title'] = 'Le titre ne doit pas dépasser 255 caractères.';
        }

        // Validation de la catégorie
        if (empty($media['id_category'])) {
            $errors['id_category'] = 'La catégorie est requise.';
        } elseif (!is_numeric($media['id_category'])) {
            $errors['id_category'] = 'Identifiant de catégorie invalide.';
        }

        // Validation de la date de publication
        $publishDate = $media['date'];
        if (empty($publishDate)) {
            $errors['date'] = 'La date de publication est requise.';
        }

        return $errors;
    }
}
