<?php

namespace App\Model;

use PDO;

class VideosManager extends AbstractManager
{
    public const TABLE = 'videos';

    /**
     * Insert new item in database
     */
    public function insert(array $item): int
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE .
            "(`title`, `picture`, `description`, `director`, `date`, `duration`,
            `quantities`, `id_category`, `id_types`)
            VALUES (:title, :picture, :description, :director, :date, :duration,
            :quantities, :id_category, :id_types)");

        $statement->bindValue(':title', $item['title'], PDO::PARAM_STR);
        $statement->bindValue(':picture', $item['picture'], PDO::PARAM_STR);
        $statement->bindValue(':description', $item['description'], PDO::PARAM_STR);
        $statement->bindValue(':director', $item['director'], PDO::PARAM_STR);
        $statement->bindValue(':date', $item['date']);
        $statement->bindValue(':duration', $item['duration'], PDO::PARAM_INT);
        $statement->bindValue(':quantities', $item['quantities'], PDO::PARAM_INT);
        $statement->bindValue(':id_category', $item['id_category'], PDO::PARAM_INT);
        $statement->bindValue(':id_types', $item['id_types'], PDO::PARAM_INT);

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
            SET `title` = :title, `picture` = :picture, `description` = :description, `director` = :director,
            `date` = :date, `duration` = :duration, `quantities` = :quantities, `id_category` = :id_category,
            `id_types` = :id_types
            WHERE id=:id"
        );

        $statement->bindValue(':id', $item['id'], PDO::PARAM_INT);
        $statement->bindValue(':title', $item['title'], PDO::PARAM_STR);
        $statement->bindValue(':picture', $item['picture'], PDO::PARAM_STR);
        $statement->bindValue(':description', $item['description'], PDO::PARAM_STR);
        $statement->bindValue(':director', $item['director'], PDO::PARAM_STR);
        $statement->bindValue(':date', $item['date']);
        $statement->bindValue(':duration', $item['duration'], PDO::PARAM_INT);
        $statement->bindValue(':quantities', $item['quantities'], PDO::PARAM_INT);
        $statement->bindValue(':id_category', $item['id_category'], PDO::PARAM_INT);
        $statement->bindValue(':id_types', $item['id_types'], PDO::PARAM_INT);

        return $statement->execute();
    }

    public function getCategoriesByVideoId(int $videoId): array
    {
        $statement = $this->pdo->prepare("
            SELECT c.name
            FROM categories c
            JOIN videos v ON c.id = v.id_category
            WHERE v.id = :video_id
        ");
        $statement->bindValue(':video_id', $videoId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    public function selectByCategory(string $category): array
    {
        $statement = $this->pdo->prepare("
            SELECT v.*, TRIM(SUBSTRING_INDEX(c.name, 'Video ', -1)) AS category
            FROM videos v
            JOIN categories c ON v.id_category = c.id
            WHERE c.name = :category
        ");
        $statement->bindValue(':category', $category, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTypesByVideoId(int $videoId): array
    {
        $statement = $this->pdo->prepare("
            SELECT t.name
            FROM types t
            JOIN videos v ON t.id = v.id_types
            WHERE v.id = :video_id
        ");
        $statement->bindValue(':video_id', $videoId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }



    public function selectByType(string $type): array
    {
        $statement = $this->pdo->prepare("
            SELECT v.*, t.name AS type
            FROM videos v
            JOIN types t ON v.id_types = t.id
            WHERE t.name = :type
        ");
        $statement->bindValue(':type', $type, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function selectByCategoryAndType(?string $category, string $type): array
    {
        $typesManager = new TypesManager();
        $typeId = $typesManager->getTypeIdByName($type);

        if ($typeId === null) {
            return [];
        }

        if ($category === null || $category === 'Tout') {
            // Si la catégorie est "Tout", renvoie tous les médias de ce type
            $statement = $this->pdo->prepare("
            SELECT v.*, t.name AS type, TRIM(SUBSTRING_INDEX(c.name, 'Video ', -1)) AS category
            FROM videos v
            JOIN categories c ON v.id_category = c.id
            JOIN types t ON v.id_types = t.id
            WHERE t.id = :type_id
        ");
            $statement->bindValue(':type_id', $typeId, PDO::PARAM_INT);
        } else {
            $statement = $this->pdo->prepare("
            SELECT v.*, t.name AS type, TRIM(SUBSTRING_INDEX(c.name, 'Video ', -1)) AS category
            FROM videos v
            JOIN categories c ON v.id_category = c.id
            JOIN types t ON v.id_types = t.id
            WHERE c.name = :category AND t.id = :type_id
        ");
            $statement->bindValue(':category', 'Video ' . $category, PDO::PARAM_STR);
            $statement->bindValue(':type_id', $typeId, PDO::PARAM_INT);
        }

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
