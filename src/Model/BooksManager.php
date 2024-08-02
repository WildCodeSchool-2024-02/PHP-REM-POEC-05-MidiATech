<?php

namespace App\Model;

use PDO;

// La classe BooksManager est responsable de la gestion des opérations de base de données pour les livres.
class BooksManager extends AbstractManager
{
    // Déclaration de la constante TABLE qui contient le nom de la table dans la base de données.
    public const TABLE = 'books';

    // Méthode pour insérer un nouveau livre dans la base de données.
    public function insert(array $item): int
    {
        // Préparation de la requête SQL d'insertion.
        $statement = $this->pdo->prepare(
            "INSERT INTO " . self::TABLE .
                "(`title`, `picture`, `description`, `author`, `date`, `pages`, `quantities`, `id_category`)
            VALUES (:title, :picture, :description, :author, :date, :pages, :quantities, :id_category)"
        );

        // Liaison des valeurs aux paramètres de la requête.
        $statement->bindValue(':title', $item['title'], PDO::PARAM_STR);
        $statement->bindValue(':picture', $item['picture'], PDO::PARAM_STR);
        $statement->bindValue(':description', $item['description'], PDO::PARAM_STR);
        $statement->bindValue(':author', $item['author'], PDO::PARAM_STR);
        $statement->bindValue(':date', $item['date']);
        $statement->bindValue(':pages', $item['pages'], PDO::PARAM_INT);
        $statement->bindValue(':quantities', $item['quantities'], PDO::PARAM_INT);
        $statement->bindValue(':id_category', $item['id_category'], PDO::PARAM_INT);

        // Exécution de la requête d'insertion.
        $statement->execute();

        // Retourne l'ID du dernier enregistrement inséré.
        return (int)$this->pdo->lastInsertId();
    }

    // Méthode pour mettre à jour un livre existant dans la base de données.
    public function update(array $item): bool
    {
        // Préparation de la requête SQL de mise à jour.
        $statement = $this->pdo->prepare(
            "UPDATE " . self::TABLE . "
            SET `title` = :title, `picture` = :picture, `description` = :description, `author` = :author,
            `date` = :date, `pages` = :pages, `quantities` = :quantities, `id_category` = :id_category
            WHERE `id` = :id"
        );

        // Liaison des valeurs aux paramètres de la requête.
        $statement->bindValue(':id', $item['id'], PDO::PARAM_INT);
        $statement->bindValue(':title', $item['title'], PDO::PARAM_STR);
        $statement->bindValue(':picture', $item['picture'], PDO::PARAM_STR);
        $statement->bindValue(':description', $item['description'], PDO::PARAM_STR);
        $statement->bindValue(':author', $item['author'], PDO::PARAM_STR);
        $statement->bindValue(':date', $item['date']);
        $statement->bindValue(':pages', $item['pages'], PDO::PARAM_INT);
        $statement->bindValue(':quantities', $item['quantities'], PDO::PARAM_INT);
        $statement->bindValue(':id_category', $item['id_category'], PDO::PARAM_INT);

        // Exécution de la requête de mise à jour.
        return $statement->execute();
    }

    // Méthode pour sélectionner des livres par catégorie.
    public function selectByCategory(string $category): array
    {
        // Préparation de la requête SQL pour sélectionner les livres par catégorie.
        $statement = $this->pdo->prepare("
        SELECT b.*, TRIM(SUBSTRING_INDEX(c.name, 'Book ', -1)) AS category
        FROM books b
        JOIN categories c ON b.id_category = c.id
        WHERE c.name = :category
    ");
        // Liaison de la valeur de la catégorie au paramètre de la requête.
        $statement->bindValue(':category', $category, PDO::PARAM_STR);
        $statement->execute();

        // Récupération de tous les résultats de la requête.
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    // Méthode pour incrémenter le stock d'un livre.
    public function incrementStock(int $id): bool
    {
        // Préparation de la requête SQL pour incrémenter le stock.
        $statement = $this->pdo->prepare("
            UPDATE " . self::TABLE . " 
            SET quantities = quantities + 1 
            WHERE id = :id
        ");
        // Liaison de l'ID du livre au paramètre de la requête.
        $statement->bindParam(':id', $id, PDO::PARAM_INT);

        // Exécution de la requête de mise à jour.
        return $statement->execute();
    }

    // Méthode pour décrémenter le stock d'un livre.
    public function changeStock(int $id): bool
    {
        // Préparation de la requête SQL pour décrémenter le stock.
        $statement = $this->pdo->prepare("
            UPDATE " . self::TABLE . " 
            SET quantities = quantities - 1
            WHERE id = :id
        ");
        // Liaison de l'ID du livre au paramètre de la requête.
        $statement->bindParam(':id', $id, PDO::PARAM_INT);

        // Exécution de la requête de mise à jour.
        return $statement->execute();
    }

    // Méthode pour mettre à jour le stock d'un livre avec une valeur spécifique.
    public function updateStock(int $mediaId, int $newStock): bool
    {
        // Préparation de la requête SQL pour mettre à jour le stock.
        $statement = $this->pdo->prepare("
            UPDATE " . self::TABLE . " SET quantities = :stock WHERE id = :id
        ");
        // Liaison des valeurs de stock et de l'ID au paramètre de la requête.
        $statement->bindParam(':stock', $newStock, PDO::PARAM_INT);
        $statement->bindParam(':id', $mediaId, PDO::PARAM_INT);

        // Exécution de la requête de mise à jour.
        return $statement->execute();
    }
}
