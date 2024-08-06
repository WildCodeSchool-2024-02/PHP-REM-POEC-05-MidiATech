<?php

namespace App\Model;

use PDO;

class CategoriesManager extends AbstractManager
{
    public const TABLE = 'categories';

    public function getCategoriesByBookId(int $bookId): array
    {
        $statement = $this->pdo->prepare("
        SELECT TRIM(SUBSTRING_INDEX(c.name, 'books ', -1)) AS name
        FROM categories c
        JOIN books b ON c.id = b.id_category
        WHERE b.id = :book_id
    ");
        $statement->bindValue(':book_id', $bookId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }



    public function getAllBookCategories(): array
    {
        $statement = $this->pdo->query("
        SELECT id, TRIM(SUBSTRING_INDEX(name, 'books ', -1)) AS name
        FROM " . self::TABLE . "
        WHERE name LIKE 'books %'
    ");

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllVideoCategories(): array
    {
        $statement = $this->pdo->query("
        SELECT id, TRIM(SUBSTRING_INDEX(name, 'videos ', -1)) AS name
        FROM " . self::TABLE . "
        WHERE name LIKE 'videos %'
    ");

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAllMusicCategories(): array
    {
        $statement = $this->pdo->query("
        SELECT id, TRIM(SUBSTRING_INDEX(name, 'musics ', -1)) AS name
        FROM " . self::TABLE . "
        WHERE name LIKE 'musics %'
    ");

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoriesByVideoId(int $videoId): array
    {
        $statement = $this->pdo->prepare("
        SELECT TRIM(SUBSTRING_INDEX(c.name, 'videos ', -1)) AS name
        FROM categories c
        JOIN videos v ON c.id = v.id_category
        WHERE v.id = :video_id
    ");
        $statement->bindValue(':video_id', $videoId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoriesByMusicId(int $musicId): array
    {
        $statement = $this->pdo->prepare("
        SELECT TRIM(SUBSTRING_INDEX(c.name, 'musics ', -1)) AS name
        FROM categories c
        JOIN musics m ON c.id = m.id_category
        WHERE m.id = :music_id
    ");
        $statement->bindValue(':music_id', $musicId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Insert new item in database
     */
    public function insert(array $item): int
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " (`name`) VALUES (:name)");
        $statement->bindValue(':name', $item['name'], PDO::PARAM_STR);

        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Update item in database
     */
    public function update(array $item): bool
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET `name` = :name WHERE id=:id");
        $statement->bindValue(':id', $item['id'], PDO::PARAM_INT);
        $statement->bindValue(':name', $item['name'], PDO::PARAM_STR);

        return $statement->execute();
    }
}
