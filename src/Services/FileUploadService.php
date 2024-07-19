<?php

namespace App\Services;

use Exception;
use Ramsey\Uuid\Uuid;

class FileUploadService
{
    public function uploadFile(&$errors): string
    {
        $uploadFileDir = '../public/assets/images/covers/';
        $newFileName = "";

        // Gestion de la suppression de l'image existante
        if (isset($_POST['delete_picture']) && !empty($_POST['imageActual'])) {
            $fileName = $uploadFileDir . $_POST['imageActual'];
            if (file_exists($fileName)) {
                unlink($fileName);
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
