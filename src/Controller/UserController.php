<?php

namespace App\Controller;

use App\Model\UserManager;
use PDO;

class UserController extends AbstractController
{
    public function inscription()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name']);
            $lastname = trim($_POST['lastname']);
            $mail = trim($_POST['mail']);
            $password = $_POST['password'];
            $address = trim($_POST['address']);
            $birthday = trim($_POST['birthday']);
            $profilePicture = $_FILES['profile_picture']['name'] ?? null;

            if ($name && $lastname && $mail && $password && $address && $birthday) {
                $userManager = new UserManager();
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $userRoleId = 2;

                if ($profilePicture) {
                    //gestion photo
                }

                $userManager->insert([
                    'firstname' => $name,
                    'lastname' => $lastname,
                    'birthday' => $birthday,
                    'email' => $mail,
                    'address' => $address,
                    'password' => $hashedPassword,
                    'role_id' => $userRoleId,
                ]);

                return $this->twig->render('Profile/profile.html.twig');
            } else {
                $error = 'Tous les champs sont obligatoires';
                return $this->twig->render('Profile/inscription.html.twig', ['error' => $error]);
            }
        }
        return $this->twig->render('Profile/inscription.html.twig');
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);

            $userManager = new UserManager();
            $user = $userManager->selectOneByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                setcookie('user_id', $user['id'], time() + (86400 * 3), "/"); // Cookie valable 3 jours

                return $this->twig->render('Profile/profile.html.twig', ['user' => $user]);
            } else {
                $error = 'Email ou mot de passe invalide';
                return $this->twig->render('Profile/login.html.twig', ['error' => $error]);
            }
        }

        return $this->twig->render('Profile/login.html.twig');
    }

    public function isUserLoggedIn()
    {
        return isset($_SESSION['user_id']) || isset($_COOKIE['user_id']);
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

    public function logout()
    {
        session_start();
        session_destroy();
        setcookie('user_id', '', time() - 3600, "/");
        header('Location: /login');
        exit();
    }


    public function profile()
    {
        if ($this->isUserLoggedIn()) {
            $user = $this->getUser();
            return $this->twig->render('Profile/profile.html.twig', ['user' => $user]);
        } else {
            header('Location: /login');
            exit();
        }
    }
}
