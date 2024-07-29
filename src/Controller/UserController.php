<?php

namespace App\Controller;

use App\Model\UserManager;
use App\Model\BorrowingManager;
use App\Trait\UsersTrait;

class UserController extends AbstractController
{
    use UsersTrait;

    public function register(): ?string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = array_map('trim', $_POST);
            $errors = $this->validate($user);

            if (empty($errors)) {
                $userManager = new UserManager();
                $user['password'] = password_hash($user['password'], PASSWORD_DEFAULT);
                $user['role_id'] = 2;

                $userManager->insert($user);
                header('Location:/account');
                return null;
            }

            return $this->twig->render('Account/register.html.twig', ['errors' => $errors, 'user' => $user]);
        }

        return $this->twig->render('Account/register.html.twig');
    }

    public function login(): string
    {
        $isUserLoggedIn = $this->isUserLoggedIn();

        if ($isUserLoggedIn) {
            $userRole = $this->getUserRole();

            if ($userRole === "admin") {
                header('Location: /admin');
                exit();
            }

            if ($userRole === "user") {
                header('Location: /account');
                exit();
            }

            header('Location: /login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);

            $userManager = new UserManager();
            $user = $userManager->selectOneByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                $this->startSessionIfNotStarted();

                $_SESSION['user_id'] = $user['id'];

                $userRole = $this->getUserRole();

                if ($userRole === "admin") {
                    header('Location: /admin');
                    exit();
                }

                header('Location: /account');
                exit();
            }

            $error = 'Email ou mot de passe invalide';
            return $this->twig->render('Account/login.html.twig', ['error' => $error]);
        }

        return $this->twig->render('Account/login.html.twig');
    }

    public function index()
    {
        if ($this->isUserLoggedIn()) {
            $user = $this->getUser();
            $userRole = $this->getUserRole();

            if ($user && $userRole === 'user') {
                $borrowingManager = new BorrowingManager();
                $borrowings = $borrowingManager->getUserBorrowings($user['id']);

                return $this->twig->render('Account/index.html.twig', [
                    'user' => $user,
                    'borrowings' => $borrowings,
                ]);
            }
        }

        header('Location: /login');
        exit();
    }

    public function editProfile(): ?string
    {
        if (isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
            $userManager = new UserManager();
            $user = $userManager->selectOneById($userId);

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $updatedUser = [
                    'id' => $userId,
                    'firstname' => trim($_POST['firstname']),
                    'lastname' => trim($_POST['lastname']),
                    'email' => trim($_POST['email']),
                    'address' => trim($_POST['address']),
                    'birthday' => trim($_POST['birthday'])
                ];

                $userManager->update($updatedUser);
                header('Location: /account');
                return null;
            }

            return $this->twig->render('Account/editProfile.html.twig', ['user' => $user]);
        }

        header('Location: /login');
        return null;
    }
}
