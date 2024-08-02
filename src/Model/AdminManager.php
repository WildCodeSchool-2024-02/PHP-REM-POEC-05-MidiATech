<?php

namespace App\Model;

use PDO;

class AdminManager extends AbstractManager
{
    public function selectCategoriesById(int $id): array
    {
        $query = "SELECT * FROM categories WHERE id = :id";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetch(\PDO::FETCH_ASSOC);
    }

    public function deleteCategories(int $id): void
    {
        // prepared request
        $statement = $this->pdo->prepare("DELETE FROM categories WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }

    public function updateCategories(array $item): void
    {
        $statement = $this->pdo->prepare(
            "UPDATE categories 
            SET `name` = :name 
            WHERE `id` = :id"
        );
        $statement->bindValue(':name', $item['name'], PDO::PARAM_STR);
        $statement->bindValue(':id', $item['id'], PDO::PARAM_INT);

        $statement->execute();
    }

    public function insertCategories(array $item): void
    {
        $statement = $this->pdo->prepare(
            "INSERT INTO categories (`name`)
            VALUES (:name)"
        );
        $statement->bindValue(':name', $item['name'], PDO::PARAM_STR);
        $statement->execute();
    }
}
