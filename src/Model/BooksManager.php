<?php

namespace App\Model;

use PDO;

class BooksManager extends AbstractManager
{
    public const TABLE = 'books';

    public function insert(array $item): int
    {
        $statement = $this->pdo->prepare(
            "INSERT INTO " . self::TABLE .
                "(`title`, `picture`, `description`, `author`, `date`, `pages`, `quantities`, `id_category`)
            VALUES (:title, :picture, :description, :author, :date, :pages, :quantities, :id_category)"
        );

        $statement->bindValue(':title', $item['title'], PDO::PARAM_STR);
        $statement->bindValue(':picture', $item['picture'], PDO::PARAM_STR);
        $statement->bindValue(':description', $item['description'], PDO::PARAM_STR);
        $statement->bindValue(':author', $item['author'], PDO::PARAM_STR);
        $statement->bindValue(':date', $item['date']);
        $statement->bindValue(':pages', $item['pages'], PDO::PARAM_INT);
        $statement->bindValue(':quantities', $item['quantities'], PDO::PARAM_INT);
        $statement->bindValue(':id_category', $item['id_category'], PDO::PARAM_INT);

        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }

    public function update(array $item): int
    {
        $statement = $this->pdo->prepare(
            "UPDATE " . self::TABLE . "
            SET `title` = :title, `picture` = :picture, `description` = :description, `author` = :author,
            `date` = :date, `pages` = :pages, `quantities` = :quantities, `id_category` = :id_category
            WHERE `id` = :id"
        );

        $statement->bindValue(':id', $item['id'], PDO::PARAM_INT);
        $statement->bindValue(':title', $item['title'], PDO::PARAM_STR);
        $statement->bindValue(':picture', $item['picture'], PDO::PARAM_STR);
        $statement->bindValue(':description', $item['description'], PDO::PARAM_STR);
        $statement->bindValue(':author', $item['author'], PDO::PARAM_STR);
        $statement->bindValue(':date', $item['date']);
        $statement->bindValue(':pages', $item['pages'], PDO::PARAM_INT);
        $statement->bindValue(':quantities', $item['quantities'], PDO::PARAM_INT);
        $statement->bindValue(':id_category', $item['id_category'], PDO::PARAM_INT);

        $statement->execute();
        return (int)$item['id'];
    }

    public function selectByCategory(string $category): array
    {
        $statement = $this->pdo->prepare("
        SELECT b.*, TRIM(SUBSTRING_INDEX(c.name, 'Book ', -1)) AS category
        FROM books b
        JOIN categories c ON b.id_category = c.id
        WHERE c.name = :category
    ");
        $statement->bindValue(':category', $category, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
