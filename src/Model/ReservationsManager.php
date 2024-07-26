<?php

namespace App\Model;

use PDO;

class ReservationsManager extends AbstractManager
{
    public const TABLE = 'reservations';

    /**
     * Insert new reservation in database
     */
    public function insert(array $item): int
    {
        $statement = $this->pdo->prepare(
            "INSERT INTO " . self::TABLE .
                "(`user_id`, `book_id`, `reservation_date`, `return_date`)
            VALUES (:user_id, :book_id, :reservation_date, :return_date)"
        );

        $statement->bindValue(':user_id', $item['user_id'], PDO::PARAM_INT);
        $statement->bindValue(':book_id', $item['book_id'], PDO::PARAM_INT);
        $statement->bindValue(':reservation_date', $item['reservation_date']);
        $statement->bindValue(':return_date', $item['return_date']);

        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Update reservation in database
     */
    public function update(array $item): bool
    {
        $statement = $this->pdo->prepare(
            "UPDATE " . self::TABLE . "
            SET `user_id` = :user_id, `book_id` = :book_id, 
            `reservation_date` = :reservation_date,
            `return_date` = :return_date
            WHERE `id` = :id"
        );

        $statement->bindValue(':id', $item['id'], PDO::PARAM_INT);
        $statement->bindValue(':user_id', $item['user_id'], PDO::PARAM_INT);
        $statement->bindValue(':book_id', $item['book_id'], PDO::PARAM_INT);
        $statement->bindValue(':reservation_date', $item['reservation_date']);
        $statement->bindValue(':return_date', $item['return_date']);


        return $statement->execute();
    }

    public function acceptReservation(int $id): bool
    {
        if ($id) {
            $id = 2;
        }
        return true;
    }

    public function refuseReservation(int $id): bool
    {
        if ($id) {
            $id = 2;
        }
        return true;
    }

    public function scheduleReservation(int $id, string $date): bool
    {
        if ($id && $date) {
            $id = 2;
        }
        return true;
    }
}
