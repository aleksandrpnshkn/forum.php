<?php
declare(strict_types=1);

namespace Src\Models;

use DateTime;

final class User
{
    public ?int $id = null;
    public ?string $username = null;
    public ?string $password = null;
    public ?string $avatar_path = null;
    public ?DateTime $created_at = null; // Can be null on creating
    public ?DateTime $updated_at = null;
    public ?DateTime $deleted_at = null;
}
