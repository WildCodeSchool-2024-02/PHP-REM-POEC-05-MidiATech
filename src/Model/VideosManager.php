<?php

namespace App\Model;

use PDO;

class VideosManager extends AbstractManager
{
    public const TABLE = 'videos';

    /**
     * Insérer un nouvel élément dans la base de données
     */
    public function insert(array $item): int
    {
        // Préparation de la requête d'insertion
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE .
            "(`title`, `picture`, `description`, `director`, `date`, `duration`,
            `quantities`, `id_category`, `id_types`)
            VALUES (:title, :picture, :description, :director, :date, :duration,
            :quantities, :id_category, :id_types)");

        // Liaison des valeurs aux paramètres de la requête
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
        return (int)$this->pdo->lastInsertId(); // Retourne l'ID du nouvel élément inséré
    }

    /**
     * Mettre à jour un élément dans la base de données
     */
    public function update(array $item): bool
    {
        // Préparation de la requête de mise à jour
        $statement = $this->pdo->prepare(
            "UPDATE " . self::TABLE . "
            SET `title` = :title, `picture` = :picture, `description` = :description, `director` = :director,
            `date` = :date, `duration` = :duration, `quantities` = :quantities, `id_category` = :id_category,
            `id_types` = :id_types
            WHERE id=:id"
        );

        // Liaison des valeurs aux paramètres de la requête
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

        return $statement->execute(); // Retourne vrai si la mise à jour a réussi
    }

    /**
     * Récupérer les catégories associées à un ID de vidéo
     */
    public function getCategoriesByVideoId(int $videoId): array
    {
        // Requête pour récupérer les catégories d'une vidéo par son ID
        $statement = $this->pdo->prepare("
            SELECT c.name
            FROM categories c
            JOIN videos v ON c.id = v.id_category
            WHERE v.id = :video_id
        ");
        $statement->bindValue(':video_id', $videoId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_COLUMN); // Retourne un tableau de noms de catégories
    }

    /**
     * Sélectionner les vidéos par catégorie
     */
    public function selectByCategory(string $category): array
    {
        // Requête pour récupérer les vidéos par catégorie
        $statement = $this->pdo->prepare("
            SELECT v.*, TRIM(SUBSTRING_INDEX(c.name, 'Video ', -1)) AS category
            FROM videos v
            JOIN categories c ON v.id_category = c.id
            WHERE c.name = :category
        ");
        $statement->bindValue(':category', $category, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC); // Retourne un tableau de vidéos avec leurs détails
    }

    /**
     * Récupérer les types associés à un ID de vidéo
     */
    public function getTypesByVideoId(int $videoId): array
    {
        // Requête pour récupérer les types d'une vidéo par son ID
        $statement = $this->pdo->prepare("
            SELECT t.name
            FROM types t
            JOIN videos v ON t.id = v.id_types
            WHERE v.id = :video_id
        ");
        $statement->bindValue(':video_id', $videoId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_COLUMN); // Retourne un tableau de noms de types
    }

    /**
     * Sélectionner les vidéos par type
     */
    public function selectByType(string $type): array
    {
        // Requête pour récupérer les vidéos par type
        $statement = $this->pdo->prepare("
            SELECT v.*, t.name AS type
            FROM videos v
            JOIN types t ON v.id_types = t.id
            WHERE t.name = :type
        ");
        $statement->bindValue(':type', $type, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC); // Retourne un tableau de vidéos avec leurs détails
    }

    /**
     * Sélectionner les vidéos par catégorie et type
     */
    public function selectByCategoryAndType(?string $category, string $type): array
    {
        $typesManager = new TypesManager();
        $typeId = $typesManager->getTypeIdByName($type);

        if ($typeId === null) {
            return []; // Retourne un tableau vide si le type n'existe pas
        }

        // Construction de la requête en fonction de la catégorie
        if ($category === null || $category === 'Tout') {
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
        return $statement->fetchAll(PDO::FETCH_ASSOC); // Retourne un tableau de vidéos avec leurs détails
    }

    /**
     * Augmenter le stock d'une vidéo
     */
    public function incrementStock(int $id): bool
    {
        // Requête pour augmenter le stock d'une vidéo par son ID
        $statement = $this->pdo->prepare("
            UPDATE " . self::TABLE . " 
            SET quantities = quantities + 1 
            WHERE id = :id
        ");
        $statement->bindParam(':id', $id, PDO::PARAM_INT);

        return $statement->execute(); // Retourne vrai si la mise à jour a réussi
    }

    /**
     * Modifier le stock d'une vidéo
     */
    public function changeStock(int $id): bool
    {
        // Requête pour diminuer le stock d'une vidéo par son ID
        $statement = $this->pdo->prepare("
            UPDATE " . self::TABLE . " 
            SET quantities = quantities - 1
            WHERE id = :id
        ");
        $statement->bindParam(':id', $id, PDO::PARAM_INT);

        return $statement->execute(); // Retourne vrai si la mise à jour a réussi
    }

    /**
     * Mettre à jour le stock d'une vidéo avec une nouvelle valeur
     */
    public function updateStock(int $mediaId, int $newStock): bool
    {
        // Requête pour mettre à jour le stock d'une vidéo
        $statement = $this->pdo->prepare("
            UPDATE " . self::TABLE . " SET quantities = :stock WHERE id = :id
        ");
        $statement->bindParam(':stock', $newStock, PDO::PARAM_INT);
        $statement->bindParam(':id', $mediaId, PDO::PARAM_INT);

        return $statement->execute(); // Retourne vrai si la mise à jour a réussi
    }

    public function selectTypes()
    {
        $query = "SELECT * FROM types ORDER BY name";
        return $this->pdo->query($query)->fetchAll(\PDO::FETCH_ASSOC);
    }
}
