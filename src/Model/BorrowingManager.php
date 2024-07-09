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
            id_borrowing = :id_borrowing"
        );

        $statement->bindParam(':id_borrowing', $item['id_borrowing'], PDO::PARAM_INT);
        $statement->bindParam(':id_media', $item['id_media'], PDO::PARAM_INT);
        $statement->bindParam(':media_type', $item['media_type'], PDO::PARAM_STR);
        $statement->bindParam(':date', $item['date'], PDO::PARAM_STR);

        return $statement->execute();
    }
}
