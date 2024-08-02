<?php

namespace App\Model;

use InvalidArgumentException;
use PDO;

class BorrowingManager extends AbstractManager
{
    public const TABLE = 'borrowing';

    /**
     * Insère un nouvel emprunt dans la base de données
     */
    public function insert(array $item): int
    {
        // Préparation de la requête d'insertion
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE .
            " (id_users, id_media, media_type, date)
            VALUES (:id_users, :id_media, :media_type, :date)");

        $statement->bindParam(':id_users', $item['id_users'], PDO::PARAM_INT);
        $statement->bindParam(':id_media', $item['id_media'], PDO::PARAM_INT);
        $statement->bindParam(':media_type', $item['media_type'], PDO::PARAM_STR);
        $statement->bindParam(':date', $item['date'], PDO::PARAM_STR);

        $statement->execute();
        return (int)$this->pdo->lastInsertId(); // Retourne l'ID de l'emprunt nouvellement inséré
    }

    /**
     * Récupère les emprunts d'un utilisateur donné
     */
    public function getUserBorrowings(int $userId): array
    {
        // Requête pour récupérer les emprunts non retournés d'un utilisateur spécifique
        $statement = $this->pdo->prepare("
        SELECT
            b.id AS borrowing_id,
            b.id_media,
            b.media_type,
            b.date,
            b.return_requested,  -- Inclut le champ return_requested
            b.is_returned,       -- Inclut le champ is_returned
            CASE
                WHEN b.media_type = 'book' THEN bk.title
                WHEN b.media_type = 'music' THEN ms.title
                WHEN b.media_type = 'video' THEN vd.title
            END AS media_title
        FROM
            " . self::TABLE . " b
        LEFT JOIN books bk ON b.id_media = bk.id AND b.media_type = 'book'
        LEFT JOIN musics ms ON b.id_media = ms.id AND b.media_type = 'music'
        LEFT JOIN videos vd ON b.id_media = vd.id AND b.media_type = 'video'
        WHERE b.id_users = :id_users
        AND b.is_returned = false  -- Ne sélectionne que les emprunts qui ne sont pas retournés
    ");
        $statement->bindParam(':id_users', $userId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Met à jour un emprunt dans la base de données
     */
    public function update(array $item): bool
    {
        // Préparation de la requête de mise à jour
        $statement = $this->pdo->prepare(
            "UPDATE " . self::TABLE . "
            SET id_media = :id_media, media_type = :media_type, date = :date
            WHERE id_borrowing = :id_borrowing"
        );

        $statement->bindParam(':id_borrowing', $item['id_borrowing'], PDO::PARAM_INT);
        $statement->bindParam(':id_media', $item['id_media'], PDO::PARAM_INT);
        $statement->bindParam(':media_type', $item['media_type'], PDO::PARAM_STR);
        $statement->bindParam(':date', $item['date'], PDO::PARAM_STR);

        return $statement->execute(); // Retourne vrai si la mise à jour a réussi
    }

    /**
     * Récupère tous les emprunts dans la base de données
     */
    public function getAllBorrowings(): array
    {
        // Requête pour récupérer tous les emprunts et leurs détails associés
        $statement = $this->pdo->prepare("
            SELECT
                b.id AS borrowing_id,
                b.id_media,
                b.media_type,
                b.date AS borrowing_date,
                b.return_requested,
                b.is_returned,
                CASE
                    WHEN b.media_type = 'book' THEN bk.title
                    WHEN b.media_type = 'music' THEN ms.title
                    WHEN b.media_type = 'video' THEN vd.title
                END AS title,
                u.firstname,
                u.lastname
            FROM
                borrowing b
            JOIN users u ON b.id_users = u.id
            LEFT JOIN books bk ON b.id_media = bk.id AND b.media_type = 'book'
            LEFT JOIN musics ms ON b.id_media = ms.id AND b.media_type = 'music'
            LEFT JOIN videos vd ON b.id_media = vd.id AND b.media_type = 'video'
        ");
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Supprime un emprunt de la base de données
     */
    public function deleteBorrowing(int $borrowingId): bool
    {
        // Requête pour supprimer un emprunt par ID
        $statement = $this->pdo->prepare("
            DELETE FROM " . self::TABLE . " WHERE id = :id
        ");
        $statement->bindParam(':id', $borrowingId, PDO::PARAM_INT);

        return $statement->execute(); // Retourne vrai si la suppression a réussi
    }

    /**
     * Ajoute un emprunt pour un utilisateur
     */
    public function addBorrowingsForUser(int $userId, int $idMedia, string $typeMedia): void
    {
        // Création d'un nouvel emprunt avec la date actuelle
        $borrowing =
            ['id_users' => $userId, 'id_media' => $idMedia, 'media_type' => $typeMedia, 'date' => date('Y-m-d')];

        $this->insert($borrowing); // Insère l'emprunt dans la base de données
    }

    /**
     * Met à jour le statut d'un emprunt (retourné et/ou demande de retour)
     */
    public function updateBorrowingStatus(int $id, bool $isReturned, bool $returnRequested): void
    {
        // Requête pour mettre à jour le statut d'un emprunt
        $statement = $this->pdo->prepare('UPDATE borrowing SET
         is_returned = :isReturned, return_requested = :returnRequested WHERE id = :id');
        $statement->bindValue(':isReturned', $isReturned, \PDO::PARAM_BOOL);
        $statement->bindValue(':returnRequested', $returnRequested, \PDO::PARAM_BOOL);
        $statement->bindValue(':id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }

    /**
     * Marque une demande de retour pour un emprunt
     */
    public function requestReturn(int $borrowingId): void
    {
        // Requête pour marquer un emprunt comme demandant un retour
        $statement = $this->pdo->prepare('UPDATE borrowing SET return_requested = true WHERE id = :id');
        $statement->bindValue(':id', $borrowingId, \PDO::PARAM_INT);
        $statement->execute();
    }

    /**
     * Supprime un emprunt de la base de données
     */
    public function delete(int $id): void
    {
        // Requête pour supprimer un emprunt par ID
        $statement = $this->pdo->prepare("DELETE FROM " . self::TABLE . " WHERE id=:id");
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
    }

    /**
     * Récupère un emprunt par son ID
     */
    public function getBorrowingById(int $borrowingId): ?array
    {
        // Requête pour récupérer un emprunt par ID
        $statement = $this->pdo->prepare("
            SELECT * FROM " . self::TABLE . " WHERE id = :borrowingId
        ");
        $statement->bindParam(':borrowingId', $borrowingId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Accepte le retour d'un emprunt si une demande de retour a été faite
     */
    public function acceptReturn(int $borrowingId): bool
    {
        // Requête pour marquer un emprunt comme retourné si la demande de retour est vraie
        $statement = $this->pdo->prepare("
            UPDATE " . self::TABLE . " 
            SET is_returned = true 
            WHERE id = :borrowingId AND return_requested = true
        ");
        $statement->bindParam(':borrowingId', $borrowingId, PDO::PARAM_INT);

        return $statement->execute(); // Retourne vrai si l'acceptation a réussi
    }
}
