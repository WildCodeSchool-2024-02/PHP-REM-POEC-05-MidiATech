<?php

namespace App\Model;

use PDO;

class MusicsManager extends AbstractManager
{
    public const TABLE = 'musics';

    /**
     * Insérer un nouvel élément dans la base de données
     */
    public function insert(array $item): int
    {
        // Préparation de la requête d'insertion
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE .
            "(`title`, `picture`, `singer`, `date`, `duration`, `quantities`, `id_category`)
            VALUES (:title, :picture, :singer, :date, :duration, :quantities, :id_category)");

        // Liaison des valeurs aux paramètres de la requête
        $statement->bindValue(':title', $item['title'], PDO::PARAM_STR);
        $statement->bindValue(':picture', $item['picture'], PDO::PARAM_STR);
        $statement->bindValue(':singer', $item['singer'], PDO::PARAM_STR);
        $statement->bindValue(':date', $item['date']);
        $statement->bindValue(':duration', $item['duration'], PDO::PARAM_INT);
        $statement->bindValue(':quantities', $item['quantities'], PDO::PARAM_INT);
        $statement->bindValue(':id_category', $item['id_category'], PDO::PARAM_INT);

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
            SET `title` = :title, `picture` = :picture, `singer` = :singer, `date` = :date, `duration` = :duration,
            `quantities` = :quantities, `id_category` = :id_category
            WHERE id=:id"
        );

        // Liaison des valeurs aux paramètres de la requête
        $statement->bindValue(':id', $item['id'], PDO::PARAM_INT);
        $statement->bindValue(':title', $item['title'], PDO::PARAM_STR);
        $statement->bindValue(':picture', $item['picture'], PDO::PARAM_STR);
        $statement->bindValue(':singer', $item['singer'], PDO::PARAM_STR);
        $statement->bindValue(':date', $item['date']);
        $statement->bindValue(':duration', $item['duration'], PDO::PARAM_INT);
        $statement->bindValue(':quantities', $item['quantities'], PDO::PARAM_INT);
        $statement->bindValue(':id_category', $item['id_category'], PDO::PARAM_INT);

        return $statement->execute(); // Retourne vrai si la mise à jour a réussi
    }

    /**
     * Sélectionner les musiques par catégorie
     */
    public function selectByCategory(string $category): array
    {
        // Requête pour récupérer les musiques par catégorie
        $statement = $this->pdo->prepare("
        SELECT m.*, TRIM(SUBSTRING_INDEX(c.name, 'musics ', -1)) AS category
        FROM musics m
        JOIN categories c ON m.id_category = c.id
        WHERE c.name = :category
    ");
        $statement->bindValue(':category', $category, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC); // Retourne un tableau de musiques avec leurs détails
    }

    /**
     * Augmenter le stock d'une musique
     */
    public function incrementStock(int $id): bool
    {
        // Requête pour augmenter le stock d'une musique par son ID
        $statement = $this->pdo->prepare("
            UPDATE " . self::TABLE . "
            SET quantities = quantities + 1
            WHERE id = :id
        ");
        $statement->bindParam(':id', $id, PDO::PARAM_INT);

        return $statement->execute(); // Retourne vrai si la mise à jour a réussi
    }

    /**
     * Modifier le stock d'une musique
     */
    public function changeStock(int $id): bool
    {
        // Requête pour diminuer le stock d'une musique par son ID
        $statement = $this->pdo->prepare("
            UPDATE " . self::TABLE . "
            SET quantities = quantities - 1
            WHERE id = :id
        ");
        $statement->bindParam(':id', $id, PDO::PARAM_INT);

        return $statement->execute(); // Retourne vrai si la mise à jour a réussi
    }

    /**
     * Mettre à jour le stock d'une musique avec une nouvelle valeur
     */
    public function updateStock(int $mediaId, int $newStock): bool
    {
        // Requête pour mettre à jour le stock d'une musique
        $statement = $this->pdo->prepare("
            UPDATE " . self::TABLE . " SET quantities = :stock WHERE id = :id
        ");
        $statement->bindParam(':stock', $newStock, PDO::PARAM_INT);
        $statement->bindParam(':id', $mediaId, PDO::PARAM_INT);

        return $statement->execute(); // Retourne vrai si la mise à jour a réussi
    }
}
