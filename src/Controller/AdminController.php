<?php

namespace App\Controller;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class AdminController extends AbstractController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function index(): ?string
    {
        if ($this->isUserLoggedIn()) {
            $userRole = $this->getUserRole();

            if ($userRole && $userRole === self::ADMIN) {
                $borrowings = $this->managers->borrowingManager->getAllBorrowings();
                return $this->twig->render('Admin/index.html.twig', ['borrowings' => $borrowings]);
            }
        }

        $this->redirect('/login');
        return null;
    }
}
