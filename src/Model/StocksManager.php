<?php

namespace App\Model;

use PDO;
use Exception;

class StocksManager extends AbstractManager
{
    public const TABLE_BOOKS = 'books';
    public const TABLE_MUSICS = 'musics';
    public const TABLE_VIDEOS = 'videos';


    /**
     * Update stock of a media item
     */
    public function updateStock(int $id, int $stock, string $type): bool
    {
        switch ($type) {
            case 'book':
                $table = self::TABLE_BOOKS;
                break;
            case 'music':
                $table = self::TABLE_MUSICS;
                break;
            case 'video':
                $table = self::TABLE_VIDEOS;
                break;
            default:
                throw new Exception("Invalid media type");
        }

        $statement = $this->pdo->prepare(
            "UPDATE $table SET `stock` = :stock WHERE `id` = :id"
        );

        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->bindValue(':stock', $stock, PDO::PARAM_INT);

        return $statement->execute();
    }

    /**
     * Get stock of a media item
     */
    public function getStock(int $id, string $type): int
    {
        switch ($type) {
            case 'book':
                $table = self::TABLE_BOOKS;
                break;
            case 'music':
                $table = self::TABLE_MUSICS;
                break;
            case 'video':
                $table = self::TABLE_VIDEOS;
                break;
            default:
                throw new Exception("Invalid media type");
        }

        $statement = $this->pdo->prepare(
            "SELECT `stock` FROM $table WHERE `id` = :id"
        );

        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        return $result ? (int)$result['stock'] : 0;
    }

    /**
     * Increment stock of a media item
     */
    public function incrementStock(int $id, int $amount, string $type): bool
    {
        $currentStock = $this->getStock($id, $type);
        $newStock = $currentStock + $amount;

        return $this->updateStock($id, $newStock, $type);
    }

    /**
     * Decrement stock of a media item
     */
    public function decrementStock(int $id, int $amount, string $type): bool
    {
        $currentStock = $this->getStock($id, $type);
        $newStock = max(0, $currentStock - $amount);

        return $this->updateStock($id, $newStock, $type);
    }
}
