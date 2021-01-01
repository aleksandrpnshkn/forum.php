<?php
declare(strict_types=1);

namespace Src\Repositories;

use DateTime;
use PDOException;
use Src\Core\Database;
use Src\Models\User;

class UserRepository
{
    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getById(int $id) : User|false
    {
        $stmt = $this->db->dbh->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $userData = $stmt->fetch();

        return $userData
            ? new User($userData)
            : false;
    }

    public function insert(User $user) : bool
    {
        try {
            $this->db->dbh
                ->prepare('
                    INSERT INTO users (username, password, avatar_path)
                    VALUES (:username, :password, :avatar_path);
                ')
                ->execute([
                    ':username' => $user->username,
                    ':password' => $user->password,
                    ':avatar_path' => $user->avatar_path,
                ]);

            $user->id = (int)$this->db->dbh->lastInsertId();
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            return false;
        }

        return true;
    }

    public function update(User $user) : bool
    {
        try {
            return $this->db->dbh
                ->prepare('
                    UPDATE users
                    SET
                        username = :username,
                        password = :password,
                        avatar_path = :avatar_path,
                        updated_at = :updated_at,
                        deleted_at = :deleted_at
                    WHERE id = :id;
                ')
                ->execute([
                    ':id' => $user->id,
                    ':username' => $user->username,
                    ':password' => $user->password,
                    ':avatar_path' => $user->avatar_path,
                    ':updated_at' => (new DateTime())->format('Y-m-d H:i:s'),
                    ':deleted_at' => $user->deleted_at?->getTimestamp(),
                ]);
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            return false;
        }
    }

    public function delete(int $id) : bool
    {
        return $this->db->dbh
            ->prepare('DELETE FROM users WHERE id = :id')
            ->execute([':id' => $id]);
    }
}