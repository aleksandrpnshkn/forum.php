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

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function getById(int $id) : ?User
    {
        return $this->getBy('id', $id);
    }

    public function getByUsername(string $username) : ?User
    {
        return $this->getBy('username', $username);
    }

    public function getByEmail(string $email) : ?User
    {
        return $this->getBy('email', $email);
    }

    /**
     * Only $value can be provided by user!
     */
    private function getBy(string $name, mixed $value) : ?User
    {
        if (! preg_match('/^[a-z_]+$/', $name)) {
            throw new \InvalidArgumentException('Column name contains bad chars');
        }

        $stmt = $this->db->dbh->prepare("SELECT * FROM users WHERE $name = :$name");
        $stmt->execute([":$name" => $value]);
        $userData = $stmt->fetch();

        if (! $userData) {
            return null;
        }

        $user = new User();
        $user->id = $userData['id'] ? (int)$userData['id'] : null;
        $user->username = $userData['username'];
        $user->email = $userData['email'];
        $user->password = $userData['password'];
        $user->avatar_path = $userData['avatar_path'];
        $user->created_at = $userData['created_at']
            ? new DateTime($userData['created_at'])
            : null;
        $user->updated_at = $userData['updated_at']
            ? new DateTime($userData['updated_at'])
            : null;
        $user->deleted_at = $userData['deleted_at']
            ? new DateTime($userData['deleted_at'])
            : null;

        return $user;
    }

    public function insert(User $user) : bool
    {
        try {
            $this->db->dbh
                ->prepare('
                    INSERT INTO users (username, email, password, avatar_path)
                    VALUES (:username, :email, :password, :avatar_path);
                ')
                ->execute([
                    ':username' => $user->username,
                    ':email' => $user->email,
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
                        email = :email,
                        password = :password,
                        avatar_path = :avatar_path,
                        updated_at = :updated_at,
                        deleted_at = :deleted_at
                    WHERE id = :id;
                ')
                ->execute([
                    ':id' => $user->id,
                    ':username' => $user->username,
                    ':email' => $user->email,
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