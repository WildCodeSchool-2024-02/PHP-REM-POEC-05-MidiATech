<?php

namespace App\Controller;

use App\Model\BorrowingManager;

class AdminController extends AbstractController
{
    public function index()
    {
        if ($this->isUserLoggedIn()) {
            $user = $this->getUser();
            $userRole = $this->getUserRole();

            if ($user && $userRole === 'admin') {
                $borrowingManager = new BorrowingManager();
                $borrowings = $borrowingManager->getAllBorrowings();
                return $this->twig->render('Admin/index.html.twig', ['borrowings' => $borrowings]);
            }
        }

        header('Location: /login');
        exit();
    }
}
