<?php

namespace App\Controller;

use App\Trait\UsersTrait;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class UserController extends AbstractController
{
    use UsersTrait;

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function index(): ?string
    {
        if ($this->isUserLoggedIn()) {
            $user = $this->getUser();
            $userRole = $this->getUserRole();

            if ($userRole && $userRole === self::USER) {
                $borrowings = $this->managers->borrowingManager->getUserBorrowings($user['id']);

                return $this->twig->render('Account/index.html.twig', [
                    'user' => $user,
                    'borrowings' => $borrowings,
                ]);
            }
        }

        $this->redirect('/login');
        return null;
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function editProfile(): ?string
    {
        if ($this->isUserLoggedIn()) {
            $userId = $this->getUserId();
            $user = $this->managers->userManager->selectOneById($userId);

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $updatedUser = [
                    'id' => $userId,
                    $user = array_map('trim', $_POST)
                ];

                //$errors = $this->validate($user);

                $this->managers->userManager->update($updatedUser);
                $this->redirect('/account');
            }

            return $this->twig->render('Account/editProfile.html.twig', ['user' => $user]);
        }

        $this->redirect('/login');
        return null;
    }
}
