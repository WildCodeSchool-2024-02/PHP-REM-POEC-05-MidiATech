<?php

namespace App\Controller;

use App\Trait\UsersTrait;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ConnectController extends AbstractController
{
    use UsersTrait;

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function register(): ?string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = array_map('trim', $_POST);
            $errors = $this->validate($user);

            if (empty($errors)) {
                $user['password'] = password_hash($user['password'], PASSWORD_DEFAULT);
                $user['role_id'] = 2;

                $this->managers->userManager->insert($user);
                $this->redirect('/account');
            }

            return $this->twig->render('Account/register.html.twig', ['errors' => $errors, 'user' => $user]);
        }

        return $this->twig->render('Account/register.html.twig');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function login(): string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->processLogin();
        }

        // $this->redirectIfLoggedIn();  // DRY principle, used in processLogin as well, consider applying here.

        return $this->twig->render('Account/login.html.twig');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    private function processLogin(): string
    {
        $this->redirectIfLoggedIn();

        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        $user = $this->managers->userManager->selectOneByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            $this->logInUser($user);
        }

        return $this->twig->render('Account/login.html.twig', ['error' => 'Email ou mot de passe invalide']);
    }

    private function logInUser($user): void
    {
        $this->managers->sessionManager->set('user_id', $user['id']);
        $this->redirectByUserRole();
    }

    private function redirectByUserRole(): void
    {
        if ($this->getUserRole() === self::ADMIN) {
            $this->redirect('/admin');
        }

        $this->redirect('/account');
    }

    private function redirectIfLoggedIn(): void
    {
        if ($this->isUserLoggedIn()) {
            $this->redirectByUserRole();
            exit;
        }
    }

    public function logout(): void
    {
        $this->managers->sessionManager->remove(['user_id', 'user', 'userRole']);
        $this->redirect('/login');
    }
}
