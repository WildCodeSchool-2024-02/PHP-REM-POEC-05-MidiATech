<?php

namespace App\Model;

use PDO;

class TypesManager extends AbstractManager
{
    public const TABLE = 'types';

    public function getTypesByVideoId($videoId): false|array
    {
        $statement = $this->pdo->prepare("
            SELECT t.*
            FROM `midiATech`.`types` t
            JOIN `midiATech`.`videos` v ON t.id = v.id_types
            WHERE v.id = :video_id
        ");

        $statement->bindValue(':video_id', $videoId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Insert new item in database
     */
    public function insert(array $item): int
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " (`name`) VALUES (:name)");
        $statement->bindValue(':name', $item['name'], PDO::PARAM_STR);

        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Update item in database
     */
    public function update(array $item): bool
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET `name` = :name WHERE id=:id");
        $statement->bindValue(':id', $item['id'], PDO::PARAM_INT);
        $statement->bindValue(':title', $item['title'], PDO::PARAM_STR);

        return $statement->execute();
    }
}
