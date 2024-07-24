<?php

namespace App\Model;

use PDO;

class BorrowingManager extends AbstractManager
{
    public const TABLE = 'borrowing';

    /**
     * Insert new item in database
     */
    public function insert(array $item): int
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE .
            " (id_users, id_media, media_type, date)
            VALUES (:id_users, :id_media, :media_type, :date)");

        $statement->bindParam(':id_users', $item['id_users'], PDO::PARAM_INT);
        $statement->bindParam(':id_media', $item['id_media'], PDO::PARAM_INT);
        $statement->bindParam(':media_type', $item['media_type'], PDO::PARAM_STR);
        $statement->bindParam(':date', $item['date'], PDO::PARAM_STR);

        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Update item in database
     */
    public function update(array $item): bool
    {
        $statement = $this->pdo->prepare(
            "UPDATE " . self::TABLE . "
            SET id_media = :id_media, media_type = :media_type, date = :date
            WHERE id_borrowing = :id_borrowing"
        );

        $statement->bindParam(':id_borrowing', $item['id_borrowing'], PDO::PARAM_INT);
        $statement->bindParam(':id_media', $item['id_media'], PDO::PARAM_INT);
        $statement->bindParam(':media_type', $item['media_type'], PDO::PARAM_STR);
        $statement->bindParam(':date', $item['date'], PDO::PARAM_STR);

        return $statement->execute();
    }

    /**
     * Get borrowings for a specific user
     */
    public function getUserBorrowings(int $userId): array
    {
        $statement = $this->pdo->prepare(
            "SELECT * FROM " . self::TABLE . " WHERE id_users = :id_users"
        );
        $statement->bindParam(':id_users', $userId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ceci est un test modifier pour tout media
     */
    public function addBorrowingsForUser(int $userId, int $idMedia, string $typeMedia): void
    {
        $borrowing = 
            ['id_users' => $userId, 'id_media' => $idMedia, 'media_type' => $typeMedia, 'date' => date('Y-m-d')];
        

            $this->insert($borrowing);
        
    }

    public function delete(int $id): void
    {
        $statement = $this->pdo->prepare("DELETE FROM " . self::TABLE . " WHERE id=:id");
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
    }
}
