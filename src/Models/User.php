<?php
declare(strict_types=1);

namespace Src\Models;

use DateTime;

final class User
{
    public ?int $id;
    public string $username;
    public string $password;
    public ?string $avatar_path;
    public ?DateTime $created_at; // Can be null on creating
    public ?DateTime $updated_at;
    public ?DateTime $deleted_at;

    public function __construct(array $userData)
    {
        $this->id = isset($userData['id']) ? (int)$userData['id'] : null;
        $this->username = $userData['username'];
        $this->password = $userData['password'];
        $this->avatar_path = $userData['avatar_path'] ?? null;
        $this->created_at = isset($userData['created_at']) ? new DateTime($userData['created_at']) : null;
        $this->updated_at = isset($userData['updated_at']) ? new DateTime($userData['updated_at']) : null;
        $this->deleted_at = null;
    }
}