<?php

namespace App\Model;

use PDO;

/**
 * Classe abstraite pour la gestion des modèles par défaut.
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
     * Récupère toutes les lignes de la table dans la base de données.
     */
    public function selectAll(string $orderBy = '', string $direction = 'ASC'): array
    {
        // Construction de la requête en fonction de la table
        $query = match (static::TABLE) {
            'books' => 'SELECT books.*, categories.name AS category FROM books
                JOIN categories ON books.id_category = categories.id',

            'musics' => 'SELECT musics.*, categories.name AS category FROM musics
                JOIN categories ON musics.id_category = categories.id',

            'videos' => 'SELECT videos.*, categories.name AS category, types.name AS type FROM videos
                JOIN categories ON videos.id_category = categories.id JOIN types ON videos.id_types = types.id',

            'borrowing' => "SELECT b.id, u.id AS user_id, u.firstname, u.lastname, u.birthday,
                        u.address, u.email, b.id_media, b.media_type,
                CASE b.media_type
                    WHEN 'book' THEN (SELECT title FROM books WHERE id = b.id_media)
                    WHEN 'music' THEN (SELECT title FROM musics WHERE id = b.id_media)
                    WHEN 'video' THEN (SELECT title FROM videos WHERE id = b.id_media)
                END AS media_title, b.date
                FROM borrowing b
                JOIN users u ON b.id_users = u.id",

            default => 'SELECT * FROM ' . static::TABLE,
        };

        // Ajout d'un tri si spécifié
        if ($orderBy) {
            $query .= ' ORDER BY ' . $orderBy . ' ' . $direction;
        }

        return $this->pdo->query($query)->fetchAll();
    }

    /**
     * Récupère une seule ligne de la base de données par ID.
     */
    public function selectOneById(int $id): ?array
    {
        // Préparation de la requête en fonction de la table
        $statement = match (static::TABLE) {
            'books' => $this->pdo->prepare("
                SELECT books.*, categories.name AS category
                FROM books
                JOIN categories ON books.id_category = categories.id
                WHERE books.id = :id"),

            'musics' => $this->pdo->prepare("SELECT musics.*, categories.name AS category
                FROM musics
                JOIN categories ON musics.id_category = categories.id
                WHERE musics.id = :id"),

            'videos' => $this->pdo->prepare("
                SELECT videos.*, categories.name AS category, types.name AS type
                FROM videos
                JOIN categories ON videos.id_category = categories.id
                JOIN types ON videos.id_types = types.id
                WHERE videos.id = :id"),

            'borrowing' => $this->pdo->prepare("
                SELECT b.id, u.id AS user_id, u.firstname, u.lastname, u.birthday,
                       u.address, u.email, b.id_media, b.media_type,
                CASE b.media_type
                    WHEN 'book' THEN (SELECT title FROM books WHERE id = b.id_media)
                    WHEN 'music' THEN (SELECT title FROM musics WHERE id = b.id_media)
                    WHEN 'video' THEN (SELECT title FROM videos WHERE id = b.id_media)
                END AS media_title, b.date
                FROM borrowing b
                JOIN users u ON b.id_users = u.id
                WHERE b.id = :id"),

            default => $this->pdo->prepare("SELECT * FROM " . static::TABLE . " WHERE id = :id"),
        };

        $statement->bindValue(':id', $id, \PDO::PARAM_INT);
        $statement->execute();

        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    /**
     * Supprime une ligne de la base de données par ID.
     */
    public function delete(int $id): void
    {
        // Requête préparée pour supprimer un enregistrement par ID
        $statement = $this->pdo->prepare("DELETE FROM " . static::TABLE . " WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }

    /**
     * Vérifie si une colonne existe dans la table.
     */
    public function columnExists($columnName): bool
    {
        $statement = $this->pdo->prepare("SHOW COLUMNS FROM " . static::TABLE . " LIKE :columnName");
        $statement->bindValue(':columnName', $columnName, \PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetch() !== false;
    }

    /**
     * Recherche dans la table en fonction d'un terme.
     */
    public function search($searchTerm)
    {
        $columnsToSearch = ['author', 'singer', 'director', 'id_types'];
        $searchQuery = "SELECT * FROM " . static::TABLE . " WHERE title LIKE :searchTerm";

        $conditions = [];

        foreach ($columnsToSearch as $column) {
            if ($this->columnExists($column)) {
                if ($column === 'id_types') {
                    $searchQuery = "SELECT * FROM " . static::TABLE . "
        JOIN types ON videos.id_types = types.id WHERE title LIKE :searchTerm";
                }
                $conditions[] = "$column LIKE :searchTerm";
            }
        }

        if (!empty($conditions)) {
            $searchQuery .= " OR (" . implode(' OR ', $conditions) . ")";
        }

        $statement = $this->pdo->prepare($searchQuery);
        $statement->bindValue(':searchTerm', '%' . $searchTerm . '%', \PDO::PARAM_STR);

        try {
            $statement->execute();
            return $statement->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            die('Search query failed: ' . $e->getMessage());
        }
    }

    /**
     * Sélectionne la ligne la plus récente de la table.
     */
    public function selectMostRecent()
    {
        $query = "SELECT * FROM " . static::TABLE . " ORDER BY date DESC limit 1";

        return $this->pdo->query($query)->fetch();
    }

    /**
     * Sélectionne les enregistrements filtrés par titre et auteur.
     */
    public function selectFiltered(string $title = '', string $author = '', string $type = ''): array
    {
        $query = "SELECT * FROM " . static::TABLE . " WHERE 1=1";

        if ($title) {
            $query .= " AND title LIKE :title";
        }

        if ($author) {
            switch ($type) {
                case 'book':
                    $query .= " AND author LIKE :author";
                    break;
                case 'music':
                    $query .= " AND singer LIKE :author";
                    break;
                case 'video':
                    $query .= " AND director LIKE :author";
                    break;
            }
        }

        $statement = $this->pdo->prepare($query);

        if ($title) {
            $statement->bindValue(':title', '%' . $title . '%', PDO::PARAM_STR);
        }

        if ($author) {
            $statement->bindValue(':author', '%' . $author . '%', PDO::PARAM_STR);
        }

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
