<?php
declare(strict_types=1);

namespace Src\Core;

use Src\Models\User;
use Src\Repositories\UserRepository;

final class Auth
{
    private ?User $user = null;
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->retrieveCurrentUser();
    }

    public function getUser() : ?User
    {
        return $this->user;
    }

    private function retrieveCurrentUser() : bool
    {
        // Check if already logged in
        if (isset($_SESSION['userId'])) {
            $user = $this->userRepository->getById((int)$_SESSION['userId']);
            return $user ? $this->logInUser($user) : false;
        }

        // Check on if user remembered
        $token = $_COOKIE['remember_token'] ?? null;

        if ($token) {
            $user = $this->userRepository->getByRememberToken($token);

            if ($user && ! $user->rememberTokenIsExpired()) {
                $this->logInUser($user);
                $_SESSION['userId'] = $this->user->id;
                return true;
            }
            elseif ($user && $user->rememberTokenIsExpired()) {
                $this->forgetUser();
            }
        }

        return false;
    }

    private function forgetUser() : void
    {
        $this->user->resetRememberToken();
        $this->userRepository->update($this->user);
        setcookie('remember_token', '', 1, '/', '', true, true);
    }

    public function logInUser(User $user) : bool
    {
        $this->user = $user;
        $_SESSION['userId'] = $this->user->id;
        return true;
    }

    public function logOut() : bool
    {
        $this->forgetUser();
        $this->user = null;
        $_SESSION['userId'] = null;
        return true;
    }

    public function isLoggedIn() : bool
    {
        return (bool)$this->user;
    }
}
