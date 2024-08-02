<?php

namespace App\Controller;

use InvalidArgumentException;
use App\Model\BorrowingManager;
use App\Model\BooksManager;
use App\Model\MusicsManager;
use App\Model\VideosManager;

class BorrowingController extends AbstractController
{
    public function retour(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->isUserLoggedIn()) {
            $borrowingId = $_POST['borrowing_id'] ?? null;

            if ($borrowingId) {
                $borrowingManager = new BorrowingManager();
                $borrowingManager->requestReturn((int)$borrowingId);

                // Redirect to account page after processing
                $this->redirect('/account');
            }
        } else {
            // Redirect to login if not authenticated
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
            // Validate and convert media_type
            $validMediaTypes = ['book', 'music', 'video'];
            if (!in_array($mediaType, $validMediaTypes, true)) {
                $mediaType = match ($mediaType) {
                    'books' => 'book',
                    'musics' => 'music',
                    'videos' => 'video',
                    default => throw new InvalidArgumentException('Invalid media_type value'),
                };
            }

            // Decrease the stock of the media
            $this->updateStock($idMedia, $mediaType);

            // Add borrowing record
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

    private function updateStock(int $idMedia, string $mediaType)
    {
        switch ($mediaType) {
            case 'book':
                $manager = new BooksManager();
                break;
            case 'music':
                $manager = new MusicsManager();
                break;
            case 'video':
                $manager = new VideosManager();
                break;
            default:
                throw new InvalidArgumentException('Invalid media type');
        }

        // Change the stock for the media
        $manager->changeStock($idMedia);
    }
}
