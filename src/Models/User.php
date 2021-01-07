<?php
declare(strict_types=1);

namespace Src\Models;

use DateInterval;
use DateTime;
use JetBrains\PhpStorm\Pure;
use Src\Controllers\Controller;
use Src\Core\Validation\ValidationError;
use Src\Repositories\UserRepository;

final class User extends Model
{
    const ROLE_ADMIN = 'admin';
    const ROLE_MODERATOR = 'moderator';
    const ROLE_USER = 'user';

    private UserRepository $userRepository;

    public ?string $username = null;
    public ?string $email = null;
    public ?string $password = null;
    public ?string $avatar_path = null;
    public ?string $remember_token = null;
    public ?string $role = null;
    public ?DateTime $remember_token_expires_at = null;
    public ?DateTime $created_at = null; // Can be null on creating
    public ?DateTime $updated_at = null;
    public ?DateTime $deleted_at = null;

    #[Pure] public function __construct()
    {
        parent::__construct();
        $this->userRepository = new UserRepository();
    }

    public function getAvatarUrl() : ?string
    {
        return $this->avatar_path
            ? Controller::$uploadsDirUrl . '/' . $this->avatar_path
            : null;
    }

    public function rememberTokenIsExpired() : bool
    {
        return $this->remember_token_expires_at->getTimestamp() <= time();
    }

    public function remember(DateInterval $duration = null) : void
    {
        if (! $duration) {
            $duration = new DateInterval('P2W');
        }

        $this->remember_token = uniqid('', true);
        $this->remember_token_expires_at = (new DateTime())->add($duration);
    }

    public function resetRememberToken() : void
    {
        $this->remember_token = null;
        $this->remember_token_expires_at = null;
    }

    public function validate() : bool
    {
        if (!  in_array($this->role, self::getRoles(), true)) {
            $this->validator->errorsBag->add(new ValidationError('role', 'Wrong role'));
        }

        $this->validateUsername();
        $this->validateEmail();
        $this->validator->validateRequired('password', $this->password);

        return ! $this->hasValidationErrors();
    }

    private function validateUsername() : bool
    {
        if (! $this->validator->validateRequired('username', $this->username)) {
            // Stop because null would causing type errors in next validation calls
            return false;
        }

        if (! preg_match('/^[A-z\d_]{3,50}$/', $this->username)) {
            $this->validator->errorsBag->add(
                new ValidationError('username', 'Username can contain only letters, digits and underscore.')
            );
        }

        if ($this->userRepository->getByUsername($this->username)) {
            $this->validator->errorsBag->add(
                new ValidationError('username', 'This username already exists.')
            );
        }

        return ! $this->hasValidationErrors();
    }

    private function validateEmail() : bool
    {
        if (! $this->validator->validateRequired('email', $this->email)) {
            return false;
        }

        $this->validator->validateEmail('email', $this->email);

        if ($this->userRepository->getByEmail($this->email)) {
            $this->validator->errorsBag->add(
                new ValidationError('email', 'This email already exists.')
            );
        }

        return ! $this->hasValidationErrors();
    }

    public function isAdmin() : bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isModerator() : bool
    {
        return $this->role === self::ROLE_USER;
    }

    public static function getRoles() : array
    {
        return [
            self::ROLE_USER,
            self::ROLE_MODERATOR,
            self::ROLE_ADMIN,
        ];
    }
}
