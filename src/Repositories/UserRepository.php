<?php
declare(strict_types=1);

namespace Src\Repositories;

use DateTime;
use Exception;
use PDOException;
use Src\Core\App;
use Src\Models\Model;
use Src\Models\User;

/**
 * @method User|null getById(int $id)
 */
class UserRepository extends Repository
{
    const TABLE_NAME = 'users';

    /**
     * @return User|null
     * @throws Exception
     */
    public function getByUsername(string $username) : ?Model
    {
        return $this->getBy('username', $username);
    }

    /**
     * @return User|null
     * @throws Exception
     */
    public function getByEmail(string $email) : ?Model
    {
        return $this->getBy('email', $email);
    }

    /**
     * @return User|null
     * @throws Exception
     */
    public function getByRememberToken(string $token) : ?Model
    {
        return $this->getBy('remember_token', $token);
    }

    public function insert(User|Model $user) : bool
    {
        try {
            self::$db->dbh
                ->prepare('
                    INSERT INTO users (username, email, password, avatar_path, remember_token, remember_token_expires_at, role)
                    VALUES (:username, :email, :password, :avatar_path, :remember_token, :remember_token_expires_at, :role);
                ')
                ->execute([
                    ':username' => $user->username,
                    ':email' => $user->email,
                    ':password' => $user->password,
                    ':avatar_path' => $user->avatar_path,
                    ':remember_token' => $user->remember_token,
                    ':remember_token_expires_at' => $user->remember_token_expires_at?->format('Y-m-d H:i:s'),
                    ':role' => $user->role,
                ]);

            $user->id = (int)self::$db->dbh->lastInsertId();
        } catch (PDOException $exception) {
            $this->log($exception->getMessage());
            return false;
        }

        return true;
    }

    public function update(User|Model $user) : bool
    {
        try {
            return self::$db->dbh
                ->prepare('
                    UPDATE users
                    SET
                        username = :username,
                        email = :email,
                        password = :password,
                        avatar_path = :avatar_path,
                        updated_at = :updated_at,
                        deleted_at = :deleted_at,
                        remember_token = :remember_token,
                        remember_token_expires_at = :remember_token_expires_at,
                        role = :role
                    WHERE id = :id;
                ')
                ->execute([
                    ':id' => $user->id,
                    ':username' => $user->username,
                    ':email' => $user->email,
                    ':password' => $user->password,
                    ':avatar_path' => $user->avatar_path,
                    ':updated_at' => (new DateTime())->format('Y-m-d H:i:s'),
                    ':deleted_at' => $user->deleted_at?->format('Y-m-d H:i:s'),
                    ':remember_token' => $user->remember_token,
                    ':remember_token_expires_at' => $user->remember_token_expires_at?->format('Y-m-d H:i:s'),
                    ':role' => $user->role,
                ]);
        } catch (PDOException $exception) {
            $this->log($exception->getMessage());
            return false;
        }
    }

    public function delete(User|Model $user) : bool
    {
        return self::$db->dbh
            ->prepare('DELETE FROM users WHERE id = :id')
            ->execute([':id' => $user->id]);
    }

    /**
     * @return User
     * @throws Exception
     */
    protected function fillInModel(array $data): Model
    {
        $user = new User();
        $user->id = $data['id'] ? (int)$data['id'] : null;
        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->password = $data['password'];
        $user->avatar_path = $data['avatar_path'];
        $user->remember_token = $data['remember_token'];
        $user->remember_token_expires_at = $data['remember_token_expires_at']
            ? new DateTime($data['remember_token_expires_at'])
            : null;
        $user->role = $data['role'];
        $this->fillInTimestamps($data, $user);
        return $user;
    }
}
