<?php

namespace App\Controller;

use App\Model\BorrowingManager;

class BorrowingController extends AbstractController
{
    public function return(int $id): void
    {
        $borrowingManager = new BorrowingManager();
        $borrowingManager->delete($id);
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }
}
