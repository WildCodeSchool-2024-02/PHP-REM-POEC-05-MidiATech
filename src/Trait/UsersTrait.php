<?php

namespace App\Trait;

trait UsersTrait
{
    public function validate($user): array
    {
        // Définir les erreurs
        $errors = [];

        // Validation du prénom
        if (empty($user['firstname'])) {
            $errors['firstname'] = 'Le prénom est requis.';
        } elseif (strlen($user['firstname']) > 80) {
            $errors['firstname'] = 'Le prénom ne doit pas dépasser 80 caractères.';
        }

        // Validation du nom
        if (empty($user['lastname'])) {
            $errors['lastname'] = 'Le nom est requis.';
        } elseif (strlen($user['lastname']) > 80) {
            $errors['lastname'] = 'Le nom ne doit pas dépasser 80 caractères.';
        }

        // Validation de la date de naissance
        if (empty($user['birthday'])) {
            $errors['birthday'] = 'Le date de naissance est requise.';
        }

        // Validation de l'email
        if (empty($user['email'])) {
            $errors['email'] = 'L\'émail est requis';
        } elseif (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'L\'émail n\'est pas valide.';
        }

        // Validation de l'adresse
        if (empty($user['address'])) {
            $errors['address'] = 'L\'adresse est requise';
        } elseif (strlen($user['lastname']) > 400) {
            $errors['address'] = 'L\'adresse ne doit pas dépasser 400 caractères.';
        }

        // Validation du mot de passe
        if (empty($user['password'])) {
            $errors['password'] = 'Le mot de passe est requis';
        }

        if (empty($user['confirmPassword'])) {
            $errors['confirmPassword'] = 'Veuillez de confirmer votre mot de passe.';
        } elseif ($user['password'] !== $user['confirmPassword']) {
            $errors['password'] = 'Les mots de passe ne correspondent pas.';
        }

        return $errors;
    }
}
