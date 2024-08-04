<?php

namespace App\Model;

use PDO;
use Exception;

class CollectionsManager extends AbstractManager
{
    public const TABLE_BOOKS = 'books';
    public const TABLE_MUSICS = 'musics';
    public const TABLE_VIDEOS = 'videos';

    /**
     * Insert new media in database
     */
    public function insert(array $item, string $type): int
    {
        switch ($type) {
            case 'book':
                $table = self::TABLE_BOOKS;
                break;
            case 'music':
                $table = self::TABLE_MUSICS;
                break;
            case 'video':
                $table = self::TABLE_VIDEOS;
                break;
            default:
                throw new Exception("Invalid media type");
        }

        $statement = $this->pdo->prepare(
            "INSERT INTO $table (`title`, `author`, `category`, `release_date`, `stock`)
            VALUES (:title, :author, :category, :release_date, :stock)"
        );

        $statement->bindValue(':title', $item['title'], PDO::PARAM_STR);
        $statement->bindValue(':author', $item['author'], PDO::PARAM_STR);
        $statement->bindValue(':category', $item['category'], PDO::PARAM_STR);
        $statement->bindValue(':release_date', $item['release_date']);
        $statement->bindValue(':stock', $item['stock'], PDO::PARAM_INT);

        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Update media in database
     */
    public function update(array $item, string $type): bool
    {
        switch ($type) {
            case 'book':
                $table = self::TABLE_BOOKS;
                break;
            case 'music':
                $table = self::TABLE_MUSICS;
                break;
            case 'video':
                $table = self::TABLE_VIDEOS;
                break;
            default:
                throw new Exception("Invalid media type");
        }

        $statement = $this->pdo->prepare(
            "UPDATE $table
            SET `title` = :title, `author` = :author, `category` = :category, `release_date` = :release_date,
             `stock` = :stock
            WHERE `id` = :id"
        );

        $statement->bindValue(':id', $item['id'], PDO::PARAM_INT);
        $statement->bindValue(':title', $item['title'], PDO::PARAM_STR);
        $statement->bindValue(':author', $item['author'], PDO::PARAM_STR);
        $statement->bindValue(':category', $item['category'], PDO::PARAM_STR);
        $statement->bindValue(':release_date', $item['release_date']);
        $statement->bindValue(':stock', $item['stock'], PDO::PARAM_INT);

        return $statement->execute();
    }

    /**
     * Delete media from database
     */
    public function delete(int $id): void
    {
        $tables = [self::TABLE_BOOKS, self::TABLE_MUSICS, self::TABLE_VIDEOS];
        foreach ($tables as $table) {
            $statement = $this->pdo->prepare("DELETE FROM $table WHERE id = :id");
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->execute();
        }
    }

    /**
     * Select all media by type
     */
    public function selectAll(string $orderBy = '', string $direction = 'ASC'): array
    {
        $results = [];
        $tables = [self::TABLE_BOOKS, self::TABLE_MUSICS, self::TABLE_VIDEOS];
        foreach ($tables as $table) {
            $statement = $this->pdo->query("SELECT * FROM $table");
            $results = array_merge($results, $statement->fetchAll(PDO::FETCH_ASSOC));
        }

        if ($orderBy) {
            usort($results, function ($abc, $bcd) use ($orderBy, $direction) {
                if ($direction === 'ASC') {
                    return $abc[$orderBy] <=> $bcd[$orderBy];
                } else {
                    return $bcd[$orderBy] <=> $abc[$orderBy];
                }
            });
        }

        return $results;
    }
}
