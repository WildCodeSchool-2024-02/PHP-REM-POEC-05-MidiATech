<?php

namespace App\Controller;

use InvalidArgumentException;
use App\Model\BorrowingManager;

class BorrowingController extends AbstractController
{
    public function return(int $id): void
    {
        $id = 0;
        if (($_SERVER['REQUEST_METHOD'] === 'POST') && $this->isUserLoggedIn()) {
            $borrowingId = $_GET['id'] ?? null;

            if ($borrowingId) {
                $borrowingManager = new BorrowingManager();
                $borrowingManager->requestReturn((int)$borrowingId);
            }
            $this->redirect('/account');
        } else {
            $this->redirect('/login');
        }
    }

    public function addBorrowing(): void
    {
        if (!$this->isUserLoggedIn()) {
            $this->redirect('/login');
        }

        $userId = $this->getUserId();
        $idMedia = $_POST['id'] ?? null;
        $mediaType = $_POST['media_type'] ?? null;

        if ($idMedia && $mediaType) {
            // Valider et convertir media_type
            $validMediaTypes = ['book', 'music', 'video'];
            if (!in_array($mediaType, $validMediaTypes, true)) {
                $mediaType = match ($mediaType) {
                    'books' => 'book',
                    'musics' => 'music',
                    'videos' => 'video',
                    default => throw new InvalidArgumentException('Invalid media_type value'),
                };
            }

            $this->managers->borrowingManager->addBorrowingsForUser($userId, $idMedia, $mediaType);
        }

        $this->redirect('/account');
    }
    public function requestReturn(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->isUserLoggedIn()) {
            $borrowingId = $_POST['id'] ?? null;
            if ($borrowingId) {
                $this->managers->borrowingManager->requestReturn((int)$borrowingId);
            }
            $this->redirect('/account');
        }

        $this->redirect('/login');
    }
}
