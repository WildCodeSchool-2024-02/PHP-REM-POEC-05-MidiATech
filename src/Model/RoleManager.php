<?php

namespace App\Model;

use PDO;

class RoleManager extends AbstractManager
{
    public const TABLE = 'roles';

    public function isUserAdmin($id): bool
    {
        $statement = $this->pdo->prepare("SELECT id FROM " . self::TABLE . " WHERE name = :name");
        $statement->execute([':name' => 'admin']);
        $adminId = $statement->fetchColumn();

        if ($adminId === false) {
            return false;
        }

        return $id === $adminId;
    }
}
