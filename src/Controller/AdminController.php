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
        // Vérifie si l'utilisateur est connecté et a le rôle d'administrateur
        if ($this->isUserLoggedIn()) {
            $userRole = $this->getUserRole();

            // Si l'utilisateur est administrateur, afficher la page d'index admin
            if ($userRole && $userRole === self::ADMIN) {
                $borrowings = $this->managers->borrowingManager->getAllBorrowings();
                return $this->twig->render('Admin/index.html.twig', ['borrowings' => $borrowings]);
            }
        }
        // Si non connecté ou non admin, redirige vers la page d'accueil
        return $this->twig->render('Home/index.html.twig');
    }

    public function categoriesMedias(): string
    {
        return $this->twig->render('Admin/categoriesMedias.html.twig');
    }

    public function reservations(): string
    {
        // Récupère toutes les réservations
        $borrowingManager = new BorrowingManager();
        $reservations = $borrowingManager->getAllBorrowings();

        // Affiche la page de gestion des réservations
        return $this->twig->render('Admin/reservations.html.twig', [
            'reservations' => $reservations
        ]);
    }

    public function deleteReservation()
    {
        // Vérifie si la requête est un POST et si l'utilisateur est connecté
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->isUserLoggedIn()) {
            $borrowingId = $_POST['borrowing_id'] ?? null;

            // Si l'ID de réservation est présent, supprime la réservation
            if ($borrowingId) {
                $borrowingManager = new BorrowingManager();
                $success = $borrowingManager->deleteBorrowing((int)$borrowingId);

                // Redirige en cas de succès ou d'erreur
                if ($success) {
                    header('Location: /admin/reservations?delete_success=1');
                    exit();
                }
            }

            header('Location: /admin/reservations?delete_error=1');
            exit();
        }

        // Redirige vers la page de connexion si non connecté
        $this->redirect('/login');
    }

    public function acceptReturn()
    {
        // Vérifie si la requête est un POST et si l'utilisateur est connecté
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->isUserLoggedIn()) {
            $borrowingId = $_POST['borrowing_id'] ?? null;

            // Si l'ID de réservation est présent, accepte le retour et met à jour le stock
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
        // Vérifie si la requête est un POST et si l'utilisateur est connecté
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->isUserLoggedIn()) {
            $mediaId = $_POST['media_id'] ?? null;
            $mediaType = $_POST['media_type'] ?? null;
            $newStock = $_POST['new_stock'] ?? null;

            // Met à jour le stock en fonction du type de média
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

                // Met à jour le stock à la nouvelle valeur
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
        // Met à jour le stock en fonction du type de média
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
        if (!$this->isUserLoggedIn()) {
            $this->redirect('/login');
            return '';
        }

        $userRole = $this->getUserRole();
        if (!$userRole || $userRole !== self::ADMIN) {
            $this->redirect('/login');
            return '';
        }

        $medias = $this->getFilteredMedias();
        return $this->twig->render('Admin/stocks.html.twig', $medias);
    }

    // Méthode pour récupérer les médias filtrés
    private function getFilteredMedias(): array
    {
        $booksManager = new BooksManager();
        $musicsManager = new MusicsManager();
        $videosManager = new VideosManager();

        // Détermine les filtres à partir des paramètres de la requête
        $type = $_GET['type'] ?? 'all';
        $searchTitle = $_GET['search_title'] ?? '';
        $searchAuthor = $_GET['search_author'] ?? '';

        $mediaManagers = [
            'book' => [$booksManager, 'author'],
            'music' => [$musicsManager, 'singer'],
            'video' => [$videosManager, 'director'],
        ];

        $medias = [];
        foreach ($mediaManagers as $mediaType => [$manager, $creatorKey]) {
            if ($type === 'all' || $type === $mediaType) {
                $medias = array_merge($medias, $this->getMediaByType(
                    $manager,
                    $mediaType,
                    $creatorKey,
                    $searchTitle,
                    $searchAuthor
                ));
            }
        }

        return [
            'medias' => $medias,
            'currentType' => $type,
            'currentTitle' => $searchTitle,
            'currentAuthor' => $searchAuthor,
        ];
    }

    // Méthode pour récupérer les médias d'un type spécifique
    private function getMediaByType(
        $manager,
        string $type,
        string $creatorKey,
        string $searchTitle,
        string $searchAuthor
    ): array {
        $mediaItems = $manager->selectFiltered($searchTitle, $searchAuthor, $type);

        return array_map(fn ($item) => [
            'type' => $type,
            'id' => $item['id'],
            'title' => $item['title'],
            'creator' => $item[$creatorKey],
            'stock' => $item['quantities']
        ], $mediaItems);
    }
}
