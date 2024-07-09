<?php

namespace App\Model;

use App\Model\Connection;
use PDO;

/**
 * Abstract class handling default manager.
 */
abstract class AbstractManager
{
    protected PDO $pdo;

    public const TABLE = '';

    public function __construct()
    {
        $connection = new Connection();
        $this->pdo = $connection->getConnection();
    }

    /**
     * Get all row from database.
     */
    public function selectAll(string $orderBy = '', string $direction = 'ASC'): array
    {
        $query = match (static::TABLE) {
            'books' => 'SELECT books.*, categories.name AS category FROM ' . static::TABLE .
                ' JOIN categories ON books.category_id = categories.id',

            'musics' => 'SELECT musics.*, categories.name AS category FROM ' . static::TABLE .
                ' JOIN categories ON musics.id_category = categories.id',

            'videos' => 'SELECT videos.*, categories.name AS category, types.name AS type FROM ' . static::TABLE .
                ' JOIN categories ON videos.id_category = categories.id JOIN types ON videos.id_types = types.id',

            default => 'SELECT * FROM ' . static::TABLE,
        };

        if ($orderBy) {
            $query .= ' ORDER BY ' . $orderBy . ' ' . $direction;
        }

        return $this->pdo->query($query)->fetchAll();
    }

    /**
     * Get one row from database by ID.
     */
    public function selectOneById(int $id): array|false
    {
        // prepared request
        $statement = match (static::TABLE) {
            'books' => $this->pdo->prepare("SELECT books.*, categories.name AS category
                FROM " . static::TABLE .
                " JOIN categories ON books.category_id = categories.id
                WHERE books.id = :id"),

            'musics' => $this->pdo->prepare("SELECT musics.*, categories.name AS category
                FROM " . static::TABLE .
                " JOIN categories ON musics.id_category = categories.id
                WHERE musics.id = :id"),

            'videos' => $this->pdo->prepare("SELECT videos.*, categories.name AS category, types.name AS type
                FROM " . static::TABLE .
                " JOIN categories ON videos.id_category = categories.id JOIN types ON videos.id_types = types.id
                WHERE videos.id = :id"),

            default => $this->pdo->prepare("SELECT * FROM " . static::TABLE . " WHERE id = :id"),
        };

        $statement->bindValue(':id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }

    /**
     * Delete row form an ID
     */
    public function delete(int $id): void
    {
        // prepared request
        $statement = $this->pdo->prepare("DELETE FROM " . static::TABLE . " WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }
}
