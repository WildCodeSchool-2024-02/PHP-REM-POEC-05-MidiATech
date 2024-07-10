<?php

namespace App\Model;

use PDO;

class MusicsManager extends AbstractManager
{
    public const TABLE = 'musics';

    /**
     * Insert new item in database
     */
    public function insert(array $item): int
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE .
            "(`title`, `picture`, `singer`, `date`, `duration`, `quantities`, `id_category`)
            VALUES (:title, :picture, :singer, :date, :duration, :quantities, :id_category)");

        $statement->bindValue(':title', $item['title'], PDO::PARAM_STR);
        $statement->bindValue(':picture', $item['picture'], PDO::PARAM_STR);
        $statement->bindValue(':singer', $item['singer'], PDO::PARAM_STR);
        $statement->bindValue(':date', $item['date']);
        $statement->bindValue(':duration', $item['duration'], PDO::PARAM_INT);
        $statement->bindValue(':quantities', $item['quantities'], PDO::PARAM_INT);
        $statement->bindValue(':id_category', $item['id_category'], PDO::PARAM_INT);

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
            SET `title` = :title, `picture` = :picture, `singer` = :singer, `date` = :date, `duration` = :duration,
            `quantities` = :quantities, `id_category` = :id_category
            WHERE id=:id"
        );

        $statement->bindValue(':id', $item['id'], PDO::PARAM_INT);
        $statement->bindValue(':title', $item['title'], PDO::PARAM_STR);
        $statement->bindValue(':picture', $item['picture'], PDO::PARAM_STR);
        $statement->bindValue(':singer', $item['singer'], PDO::PARAM_STR);
        $statement->bindValue(':date', $item['date']);
        $statement->bindValue(':duration', $item['duration'], PDO::PARAM_INT);
        $statement->bindValue(':quantities', $item['quantities'], PDO::PARAM_INT);
        $statement->bindValue(':id_category', $item['id_category'], PDO::PARAM_INT);

        return $statement->execute();
    }
}
