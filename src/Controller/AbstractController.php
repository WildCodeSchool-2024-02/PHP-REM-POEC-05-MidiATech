<?php

namespace App\Controller;

use Exception;
use Ramsey\Uuid\Uuid;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

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

    protected function uploadFile(&$errors): string
    {
        $uploadFileDir = '../public/assets/images/covers/';
        $newFileName = "";

        // Gestion de la suppression de l'image existante
        if (isset($_POST['delete_picture']) && !empty($_POST['imageActual'])) {
            $fileName = $uploadFileDir . $_POST['imageActual'];
            if (file_exists($fileName)) {
                if (unlink($fileName)) {
                    echo "Fichier supprimé avec succès.";
                } else {
                    echo "Erreur lors de la suppression du fichier.";
                    exit();
                }
            } else {
                echo "Le fichier n'existe pas : " . $fileName;
                exit();
            }
        }

        // Gestion du téléchargement de la nouvelle image
        if ($_FILES['picture']['error'] === UPLOAD_ERR_NO_FILE) {
            return $newFileName;  // Retourne un nom de fichier vide si aucun fichier n'est téléchargé
        }

        $fileTmpPath = $_FILES['picture']['tmp_name'];
        $originalFileName = pathinfo($_FILES['picture']['name'], PATHINFO_FILENAME);
        $fileExtension = pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION);
        $fileType = $_FILES['picture']['type'];

        // Extensions valides
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($fileType, $allowedTypes, true)) {
            $errors['picture'] = 'Type de fichier non autorisé. Seuls JPEG, PNG, GIF et WEBP sont acceptés.';
            return $newFileName;
        }

        // Nettoyer le nom de fichier original pour éviter les problèmes de sécurité
        $safeFileName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $originalFileName);

        // Générer un UUID pour le fichier
        $uuid = Uuid::uuid4()->toString();
        $newFileName = $uuid . '_' . $safeFileName . '.' . $fileExtension;

        // Définir le chemin de destination
        $destPath = $uploadFileDir . $newFileName;

        // Déplacer le fichier
        if (!move_uploaded_file($fileTmpPath, $destPath)) {
            $errors['picture'] = 'Erreur lors du téléchargement du fichier.';
            throw new Exception("Fichier non sauvegardé");
        }

        return $newFileName;
    }
}
