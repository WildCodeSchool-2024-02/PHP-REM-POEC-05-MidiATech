<?php

namespace App\Controller;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use App\Model\BooksManager;
use App\Model\BorrowingManager;
use App\Model\MusicsManager;
use App\Model\VideosManager;
use InvalidArgumentException;

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
        return $this->twig->render('Home/index.html.twig');
    }

    public function collections(): string
    {
        $booksManager = new BooksManager();
        $books = $booksManager->selectAll();

        $musicsManager = new MusicsManager();
        $music = $musicsManager->selectAll();

        $videosManager = new VideosManager();
        $videos = $videosManager->selectAll();

        return $this->twig->render('Admin/collections.html.twig', [
            'books' => $books,
            'music' => $music,
            'videos' => $videos
        ]);
    }

    public function reservations(): string
    {
        $borrowingManager = new BorrowingManager();
        $reservations = $borrowingManager->getAllBorrowings();

        return $this->twig->render('Admin/reservations.html.twig', [
            'reservations' => $reservations
        ]);
    }





    public function acceptReturn(int $id)
    {
        $borrowingManager = new BorrowingManager();
        $borrowing = $borrowingManager->getBorrowingById($id);

        if ($borrowing && $borrowing['return_requested']) {
            $borrowingManager->updateBorrowingStatus($id, true, false);

            // Update the stock of the returned item
            $this->updateStock($borrowing['id_media'], $borrowing['media_type']);
        }

        header('Location: /admin/reservations');
        exit();
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

        $manager->incrementStock($idMedia);
    }

    public function stocks(): string
    {
        $booksManager = new BooksManager();
        $books = $booksManager->selectAll();

        $musicsManager = new MusicsManager();
        $music = $musicsManager->selectAll();

        $videosManager = new VideosManager();
        $videos = $videosManager->selectAll();

        return $this->twig->render('Admin/stocks.html.twig', [
            'books' => $books,
            'musics' => $music,
            'videos' => $videos
        ]);
    }
}
