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

    public function selectByCategory(string $category): array
    {
        $statement = $this->pdo->prepare("
        SELECT m.*, TRIM(SUBSTRING_INDEX(c.name, 'Music ', -1)) AS category
        FROM musics m
        JOIN categories c ON m.id_category = c.id
        WHERE c.name = :category
    ");
        $statement->bindValue(':category', $category, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    public function incrementStock(int $id): bool
    {
        $statement = $this->pdo->prepare("
            UPDATE " . self::TABLE . " 
            SET quantities = quantities + 1 
            WHERE id = :id
        ");
        $statement->bindParam(':id', $id, PDO::PARAM_INT);

        return $statement->execute();
    }
    public function changeStock(int $id): bool
    {
        $statement = $this->pdo->prepare("
            UPDATE " . self::TABLE . " 
            SET quantities = quantities - 1
            WHERE id = :id
        ");
        $statement->bindParam(':id', $id, PDO::PARAM_INT);

        return $statement->execute();
    }

    public function updateStock(int $mediaId, int $newStock): bool
    {
        $statement = $this->pdo->prepare("
            UPDATE " . self::TABLE . " SET quantities = :stock WHERE id = :id
        ");
        $statement->bindParam(':stock', $newStock, PDO::PARAM_INT);
        $statement->bindParam(':id', $mediaId, PDO::PARAM_INT);

        return $statement->execute();
    }
}
