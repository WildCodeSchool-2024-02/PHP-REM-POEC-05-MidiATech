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

    public function reservations(): string
    {
        $borrowingManager = new BorrowingManager();
        $reservations = $borrowingManager->getAllBorrowings();

        return $this->twig->render('Admin/reservations.html.twig', [
            'reservations' => $reservations
        ]);
    }

    public function deleteReservation()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->isUserLoggedIn()) {
            $borrowingId = $_POST['borrowing_id'] ?? null;

            if ($borrowingId) {
                $borrowingManager = new BorrowingManager();
                $success = $borrowingManager->deleteBorrowing((int)$borrowingId);

                if ($success) {
                    header('Location: /admin/reservations?delete_success=1');
                    exit();
                }
            }

            header('Location: /admin/reservations?delete_error=1');
            exit();
        }

        $this->redirect('/login');
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



    public function acceptReturn()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->isUserLoggedIn()) {
            $borrowingId = $_POST['borrowing_id'] ?? null;

            if ($borrowingId) {
                $borrowingManager = new BorrowingManager();
                $borrowing = $borrowingManager->getBorrowingById((int)$borrowingId);

                if ($borrowing && $borrowingManager->acceptReturn((int)$borrowingId)) {
                    $this->updateStock($borrowing['id_media'], $borrowing['media_type']);
                    header('Location: /admin/reservations?success=1');
                    exit();
                }
            }

            header('Location: /admin/reservations?error=1');
            exit();
        }

        $this->redirect('/login');
    }

    public function handleUpdateStock(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->isUserLoggedIn()) {
            // Extract parameters from POST request
            $mediaId = $_POST['media_id'] ?? null;
            $mediaType = $_POST['media_type'] ?? null;
            $newStock = $_POST['new_stock'] ?? null;

            if ($mediaId && $mediaType && is_numeric($newStock)) {
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

                // Update the stock to the new value
                $manager->updateStock((int)$mediaId, (int)$newStock);
                header('Location: /admin/stocks?update_success=1');
                exit();
            }

            header('Location: /admin/stocks?update_error=1');
            exit();
        }

        $this->redirect('/login');
    }

    public function updateStock(int $idMedia, string $mediaType)
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
        if ($this->isUserLoggedIn()) {
            $userRole = $this->getUserRole();

            if ($userRole && $userRole === self::ADMIN) {
                // Initialize media managers
                $booksManager = new BooksManager();
                $musicsManager = new MusicsManager();
                $videosManager = new VideosManager();

                // Fetch all media data
                $books = $booksManager->selectAll();
                $musics = $musicsManager->selectAll();
                $videos = $videosManager->selectAll();

                // Combine all media data into a single array
                $medias = array_merge(
                    array_map(fn ($book) => [
                        'type' => 'book',
                        'id' => $book['id'],
                        'title' => $book['title'],
                        'creator' => $book['author'], // Use 'author' for books
                        'stock' => $book['quantities'] // Fetch 'quantities' as 'stock'
                    ], $books),
                    array_map(fn ($music) => [
                        'type' => 'music',
                        'id' => $music['id'],
                        'title' => $music['title'],
                        'creator' => $music['singer'], // Use 'singer' for musics
                        'stock' => $music['quantities'] // Fetch 'quantities' as 'stock'
                    ], $musics),
                    array_map(fn ($video) => [
                        'type' => 'video',
                        'id' => $video['id'],
                        'title' => $video['title'],
                        'creator' => $video['director'], // Use 'director' for videos
                        'stock' => $video['quantities'] // Fetch 'quantities' as 'stock'
                    ], $videos)
                );

                // Render the stocks page with media data
                return $this->twig->render('Admin/stocks.html.twig', [
                    'medias' => $medias
                ]);
            }
        }

        // Redirect to login if the user is not logged in or not an admin
        $this->redirect('/login');
        return '';
    }
}
