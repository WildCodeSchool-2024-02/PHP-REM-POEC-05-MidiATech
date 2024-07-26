<?php

namespace App\Controller;

use InvalidArgumentException;
use App\Model\BorrowingManager;

class BorrowingController extends AbstractController
{
    public function return(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $segments = explode('/', trim($path, '/'));
            $id = end($segments);

            if (isset($_SESSION['user_id'])) {
                $borrowingManager = new BorrowingManager();
                $borrowingManager->delete((int)$id);

                // Redirection vers le compte utilisateur après la suppression
                header('Location: /account');
                exit();
            } else {
                // Redirection vers la page de connexion si l'utilisateur n'est pas connecté
                header('Location: /login');
                exit();
            }
        }
    }

    public function addBorrowing(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        $userId = $_SESSION['user_id'];
        $idMedia = $_POST['id'] ?? null;
        $mediaType = $_POST['media_type'] ?? null;

        if ($idMedia && $mediaType) {
            // Valider et convertir media_type
            $validMediaTypes = ['book', 'music', 'video'];
            if (!in_array($mediaType, $validMediaTypes)) {
                switch ($mediaType) {
                    case 'books':
                        $mediaType = 'book';
                        break;
                    case 'musics':
                        $mediaType = 'music';
                        break;
                    case 'videos':
                        $mediaType = 'video';
                        break;
                    default:
                        throw new InvalidArgumentException('Invalid media_type value');
                }
            }

            $borrowingManager = new BorrowingManager();
            $borrowingManager->addBorrowingsForUser($userId, $idMedia, $mediaType);
        }

        header('Location: /account');
        exit();
    }
}
