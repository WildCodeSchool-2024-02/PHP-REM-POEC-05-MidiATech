<?php

namespace App\Model;

use PDO;

class UserManager extends AbstractManager
{
    public const TABLE = 'users';

    /**
     * Insert new item in database
     */
    public function insert(array $item): int
    {

        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE .
            " (firstname, lastname, birthday, email, address, password, role_id)
            VALUES (:firstname, :lastname, :birthday, :email, :address, :password, :role_id)");

        $statement->bindParam(':firstname', $item['firstname'], PDO::PARAM_STR);
        $statement->bindParam(':lastname', $item['lastname'], PDO::PARAM_STR);
        $statement->bindParam(':birthday', $item['birthday'], PDO::PARAM_STR);
        $statement->bindParam(':email', $item['email'], PDO::PARAM_STR);
        $statement->bindParam(':address', $item['address'], PDO::PARAM_STR);
        $statement->bindParam(':password', $item['password'], PDO::PARAM_STR);
        $statement->bindParam(':role_id', $item['role_id'], PDO::PARAM_INT);

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
            SET `firstname` = :firstname, `lastname` = :lastname, `birthday` = :birthday, `email` = :email,
            `address` = :address
            WHERE id=:id"
        );

        $statement->bindValue(':id', $item['id'], PDO::PARAM_INT);
        $statement->bindParam(':firstname', $item['firstname'], PDO::PARAM_STR);
        $statement->bindParam(':lastname', $item['lastname'], PDO::PARAM_STR);
        $statement->bindParam(':birthday', $item['birthday'], PDO::PARAM_STR);
        $statement->bindParam(':email', $item['email'], PDO::PARAM_STR);
        $statement->bindParam(':address', $item['address'], PDO::PARAM_STR);

        return $statement->execute();
    }

    /**
     * Get one row from database by Email.
     */

    public function selectOneByEmail(string $email): ?array
    {
        $statement = $this->pdo->prepare("SELECT * FROM " . static::TABLE . " WHERE email = :email");
        $statement->bindValue(':email', $email, PDO::PARAM_STR);
        $statement->execute();

        $user = $statement->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }


    public function getUserRole($userId)
    {
        $statement = $this->pdo->prepare(
            "SELECT roles.name as role
                FROM " . static::TABLE . "
                INNER JOIN roles ON users.role_id=roles.id
                WHERE users.id = :userId"
        );
        $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
        $statement->execute();

        $role = $statement->fetch(PDO::FETCH_ASSOC);

        return $role['role'] ?? null;
    }
}
