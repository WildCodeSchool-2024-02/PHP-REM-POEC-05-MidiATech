<?php

namespace App\Model;

use InvalidArgumentException;
use PDO;

class BorrowingManager extends AbstractManager
{
    public const TABLE = 'borrowing';

    /**
     * Insert new item in database
     */
    public function insert(array $item): int
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE .
            " (id_users, id_media, media_type, date)
            VALUES (:id_users, :id_media, :media_type, :date)");

        $statement->bindParam(':id_users', $item['id_users'], PDO::PARAM_INT);
        $statement->bindParam(':id_media', $item['id_media'], PDO::PARAM_INT);
        $statement->bindParam(':media_type', $item['media_type'], PDO::PARAM_STR);
        $statement->bindParam(':date', $item['date'], PDO::PARAM_STR);

        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }

    public function getUserBorrowings(int $userId): array
    {
        $statement = $this->pdo->prepare("
            SELECT
                b.id AS borrowing_id,
                b.id_media,
                b.media_type,
                b.date,
                CASE
                    WHEN b.media_type = 'book' THEN bk.title
                    WHEN b.media_type = 'music' THEN ms.title
                    WHEN b.media_type = 'video' THEN vd.title
                END AS media_title
            FROM
                " . self::TABLE . " b
            LEFT JOIN books bk ON b.id_media = bk.id AND b.media_type = 'book'
            LEFT JOIN musics ms ON b.id_media = ms.id AND b.media_type = 'music'
            LEFT JOIN videos vd ON b.id_media = vd.id AND b.media_type = 'video'
            WHERE b.id_users = :id_users
        ");
        $statement->bindParam(':id_users', $userId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update item in database
     */
    public function update(array $item): bool
    {
        $statement = $this->pdo->prepare(
            "UPDATE " . self::TABLE . "
            SET id_media = :id_media, media_type = :media_type, date = :date
            WHERE id_borrowing = :id_borrowing"
        );

        $statement->bindParam(':id_borrowing', $item['id_borrowing'], PDO::PARAM_INT);
        $statement->bindParam(':id_media', $item['id_media'], PDO::PARAM_INT);
        $statement->bindParam(':media_type', $item['media_type'], PDO::PARAM_STR);
        $statement->bindParam(':date', $item['date'], PDO::PARAM_STR);

        return $statement->execute();
    }





    public function getAllBorrowings(): false|array
    {
        $statement = $this->pdo->query("
        SELECT
        b.id AS borrowing_id,
        b.media_type,
        u.firstname,
        u.lastname,
        u.birthday,
        u.email,
        u.address,
        CASE
            WHEN b.media_type = 'book' THEN bk.title
            WHEN b.media_type = 'music' THEN ms.title
            WHEN b.media_type = 'video' THEN vd.title
        END AS title,
        CASE
            WHEN b.media_type = 'book' THEN bk.author
            WHEN b.media_type = 'music' THEN ms.singer
            WHEN b.media_type = 'video' THEN vd.director
        END AS media_creator,
        b.date AS borrowing_date
        FROM
        borrowing b
        JOIN users u ON b.id_users = u.id
        LEFT JOIN books bk ON b.id_media = bk.id AND b.media_type = 'book'
        LEFT JOIN musics ms ON b.id_media = ms.id AND b.media_type = 'music'
        LEFT JOIN videos vd ON b.id_media = vd.id AND b.media_type = 'video';
        ");

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ceci est un test modifier pour tout media
     */
    public function addBorrowingsForUser(int $userId, int $idMedia, string $typeMedia): void
    {
        $borrowing =
            ['id_users' => $userId, 'id_media' => $idMedia, 'media_type' => $typeMedia, 'date' => date('Y-m-d')];


        $this->insert($borrowing);
    }

    public function getBorrowingById(int $id): array
    {
        $statement = $this->pdo->prepare('SELECT * FROM borrowing WHERE id = :id');
        $statement->bindValue(':id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }

    public function updateBorrowingStatus(int $id, bool $isReturned, bool $returnRequested): void
    {
        $statement = $this->pdo->prepare('UPDATE borrowing SET
         is_returned = :isReturned, return_requested = :returnRequested WHERE id = :id');
        $statement->bindValue(':isReturned', $isReturned, \PDO::PARAM_BOOL);
        $statement->bindValue(':returnRequested', $returnRequested, \PDO::PARAM_BOOL);
        $statement->bindValue(':id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }

    public function requestReturn(int $borrowingId): void
    {
        $statement = $this->pdo->prepare('UPDATE borrowing SET return_requested = true WHERE id = :id');
        $statement->bindValue(':id', $borrowingId, \PDO::PARAM_INT);
        $statement->execute();
    }

    public function delete(int $id): void
    {
        $statement = $this->pdo->prepare("DELETE FROM " . self::TABLE . " WHERE id=:id");
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
    }
}
