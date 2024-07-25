<?php

namespace App\Trait;

trait MediasTrait
{
    public function validate($media): array
    {
        // Définir les erreurs
        $errors = [];

        // Validation de l'image
        if (!empty($media['picture'])) {
            $url = $media['picture'];

            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                $errors['title'] = 'L\'URL n\'est pas valide.';
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
