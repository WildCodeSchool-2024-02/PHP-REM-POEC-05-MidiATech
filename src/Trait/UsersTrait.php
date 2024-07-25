<?php

namespace App\Trait;

trait UsersTrait
{
    public function validate($user): array
    {
        // Définir les erreurs
        $errors = [];

        $errors['firstname'] = $this->validateFirstname($user['firstname']);
        $errors['lastname'] = $this->validateLastname($user['lastname']);
        $errors['birthday'] = $this->validateBirthday($user['birthday']);
        $errors['email'] = $this->validateEmail($user['email']);
        $errors['address'] = $this->validateAddress($user['address']);
        $errors = array_merge($errors, $this->validatePassword($user['password'], $user['confirmPassword']));

        return array_filter($errors);
    }

    private function validateFirstname($firstname): ?string
    {
        if (empty($firstname)) {
            return 'Le prénom est requis.';
        }

        if (strlen($firstname) > 80) {
            return 'Le prénom ne doit pas dépasser 80 caractères.';
        }
        return null;
    }

    private function validateLastname($lastname): ?string
    {
        if (empty($lastname)) {
            return 'Le nom est requis.';
        }

        if (strlen($lastname) > 80) {
            return 'Le nom ne doit pas dépasser 80 caractères.';
        }
        return null;
    }

    private function validateBirthday($birthday): ?string
    {
        if (empty($birthday)) {
            return 'Le date de naissance est requise.';
        }
        return null;
    }

    private function validateEmail($email): ?string
    {
        if (empty($email)) {
            return 'L\'émail est requis';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'L\'émail n\'est pas valide.';
        }
        return null;
    }

    private function validateAddress($address): ?string
    {
        if (empty($address)) {
            return 'L\'adresse est requise';
        }

        if (strlen($address) > 400) {
            return 'L\'adresse ne doit pas dépasser 400 caractères.';
        }
        return null;
    }

    private function validatePassword($password, $confirmPassword): array
    {
        $errors = [];
        if (empty($password)) {
            $errors['password'] = 'Le mot de passe est requis';
        } elseif (empty($confirmPassword)) {
            $errors['confirmPassword'] = 'Veuillez de confirmer votre mot de passe.';
        } elseif ($password !== $confirmPassword) {
            $errors['password'] = 'Les mots de passe ne correspondent pas.';
        }

        return $errors;
    }
}
