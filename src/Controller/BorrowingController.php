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
        // Vérifie si la requête est un POST et si l'utilisateur est connecté
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->isUserLoggedIn()) {
            $borrowingId = $_POST['borrowing_id'] ?? null;

            // Si l'ID de l'emprunt est présent, demande un retour
            if ($borrowingId) {
                $borrowingManager = new BorrowingManager();
                $borrowingManager->requestReturn((int)$borrowingId);

                // Redirige vers la page de compte après le traitement
                $this->redirect('/account');
            }
        } else {
            // Redirige vers la page de connexion si non authentifié
            $this->redirect('/login');
        }
    }

    public function addBorrowing(): void
    {
        // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
        if (!$this->isUserLoggedIn()) {
            $this->redirect('/login');
        }

        $userId = $this->getUserId();
        $idMedia = $_POST['id'] ?? null;
        $mediaType = $_POST['media_type'] ?? null;

        // Vérifie la validité de l'ID du média et du type de média
        if ($idMedia && $mediaType) {
            // Valide et convertit le type de média
            $validMediaTypes = ['book', 'music', 'video'];
            if (!in_array($mediaType, $validMediaTypes, true)) {
                $mediaType = match ($mediaType) {
                    'books' => 'book',
                    'musics' => 'music',
                    'videos' => 'video',
                    default => throw new InvalidArgumentException('Invalid media_type value'),
                };
            }

            // Diminue le stock du média
            $this->updateStock($idMedia, $mediaType);

            // Ajoute un enregistrement d'emprunt pour l'utilisateur
            $this->managers->borrowingManager->addBorrowingsForUser($userId, $idMedia, $mediaType);
        }

        $this->redirect('/account');
    }

    public function requestReturn(): void
    {
        // Vérifie si la requête est un POST et si l'utilisateur est connecté
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->isUserLoggedIn()) {
            $borrowingId = $_POST['id'] ?? null;
            if ($borrowingId) {
                // Demande le retour de l'emprunt
                $this->managers->borrowingManager->requestReturn((int)$borrowingId);
            }
            $this->redirect('/account');
        }

        $this->redirect('/login');
    }

    private function updateStock(int $idMedia, string $mediaType)
    {
        // Sélectionne le bon gestionnaire en fonction du type de média
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

        // Modifie le stock pour le média donné
        $manager->changeStock($idMedia);
    }
}
