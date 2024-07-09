<?php

namespace App\Model;

use PDO;

class UsersManager extends AbstractManager
{
    public const TABLE = 'users';

    /**
     * Insert new item in database
     */
    public function insert(array $item): int
    {
        $passwordHash = password_hash($item['password'], PASSWORD_DEFAULT);

        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE .
            " (firstname, lastname, birthday, email, address, password)
            VALUES (:firstname, :lastname, :birthday, :email, :address, :password)");

        $statement->bindParam(':firstname', $item['firstname'], PDO::PARAM_STR);
        $statement->bindParam(':lastname', $item['lastname'], PDO::PARAM_STR);
        $statement->bindParam(':birthday', $item['birthday'], PDO::PARAM_STR);
        $statement->bindParam(':email', $item['email'], PDO::PARAM_STR);
        $statement->bindParam(':address', $item['address'], PDO::PARAM_STR);
        $statement->bindParam(':password', $passwordHash, PDO::PARAM_STR);

        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Update item in database
     */
    public function update(array $item): bool
    {
        $passwordHash = password_hash($item['password'], PASSWORD_DEFAULT);

        $statement = $this->pdo->prepare(
            "UPDATE " . self::TABLE . "
            SET `firstname` = :firstname, `lastname` = :lastname, `birthday` = :birthday, `email` = :email,
            `address` = :address,`password` = :password
            WHERE id=:id"
        );

        $statement->bindValue(':id', $item['id'], PDO::PARAM_INT);
        $statement->bindParam(':firstname', $item['firstname'], PDO::PARAM_STR);
        $statement->bindParam(':lastname', $item['lastname'], PDO::PARAM_STR);
        $statement->bindParam(':birthday', $item['birthday'], PDO::PARAM_STR);
        $statement->bindParam(':email', $item['email'], PDO::PARAM_STR);
        $statement->bindParam(':address', $item['address'], PDO::PARAM_STR);
        $statement->bindParam(':password', $passwordHash, PDO::PARAM_STR);

        return $statement->execute();
    }
}
