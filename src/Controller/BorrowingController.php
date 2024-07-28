<?php

namespace App\Controller;

use InvalidArgumentException;

class BorrowingController extends AbstractController
{
    public function return(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->isUserLoggedIn()) {
            $borrowingId = $this->getBorrowingIdFromURI($_SERVER['REQUEST_URI']);
            $this->managers->borrowingManager->delete($borrowingId);
            $this->redirect('/account');
        }

        $this->redirect('/login');
    }

    private function getBorrowingIdFromURI(string $uri): int
    {
        $path = parse_url($uri, PHP_URL_PATH);
        $segments = explode('/', trim($path, '/'));
        return (int)end($segments);
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
}
