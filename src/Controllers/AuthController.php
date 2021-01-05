<?php
declare(strict_types=1);

namespace Src\Controllers;

use Src\Models\User;

class AuthController extends Controller
{
    public function registerForm()
    {
        if ($this->app->auth->isLoggedIn()) {
            header('Location: /');
            return;
        }

        $this->app->view->display('auth/register');
    }

    public function register()
    {
        if ($this->app->auth->isLoggedIn()) {
            header('Location: /');
            return;
        }

        $username = $_POST['username'] ?? null;
        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;
        $passwordConfirmation = $_POST['password_confirmation'] ?? null;
        $remember = isset($_POST['remember']) && $_POST['remember'] === 'on';

        if ($password !== $passwordConfirmation) {
            $this->addValidationError('password', 'Password not confirmed.');
        }

        // Show controller's validation errors
        if ($this->hasValidationErrors()) {
            $this->app->view->display('auth/register', ['errorsBag' => $this->validator->errorsBag]);
            return;
        }

        $user = new User();
        $user->username = $username;
        $user->email = $email;
        $user->password = password_hash($password, PASSWORD_DEFAULT);

        if ($remember) {
            $user->remember();
            setcookie('remember_token', $user->remember_token, $user->remember_token_expires_at->getTimestamp(), '/', '', true, true);
        }

        // Show model's validation errors
        if (! $user->validate()) {
            $this->app->view->display('auth/register', ['errorsBag' => $user->validator->errorsBag]);
            return;
        }

        if ($this->app->getUserRepository()->insert($user)) {
            header('Location: /');
        }
        else {
            $this->app->view->display('auth/register', ['message' => 'Something gone wrong']);
        }
    }

    public function logInForm()
    {
        if ($this->app->auth->isLoggedIn()) {
            header('Location: /');
            return;
        }

        $this->app->view->display('auth/login');
    }

    public function logIn()
    {
        if ($this->app->auth->isLoggedIn()) {
            header('Location: /');
            return;
        }

        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;
        $remember = isset($_POST['remember']) && $_POST['remember'] === 'on';

        if (
            $this->validator->validateEmail('email', $email)
            && $this->validator->validateRequired('password', $password)
        ) {
            $user = $this->app->getUserRepository()->getByEmail($email);

            if ($user && $this->verifyPassword($password, $user->password)) {
                $this->app->auth->logInUser($user);

                if ($remember) {
                    $user->remember();
                    setcookie('remember_token', $user->remember_token, $user->remember_token_expires_at->getTimestamp(), '/', '', true, true);
                }

                header('Location: /');
                return;
            }

            if (! $user) {
                $this->addValidationError('email', 'User not found');
            }
        }

        // Show controller's validation errors
        if ($this->hasValidationErrors()) {
            $this->app->view->display('auth/login', ['errorsBag' => $this->validator->errorsBag]);
            return;
        }
    }

    private function verifyPassword(string $password, string $hash) : bool
    {
        $isCorrect = password_verify($password, $hash);

        if (! $isCorrect) {
            $this->addValidationError('password', 'Wrong password');
        }

        return $isCorrect;
    }

    public function logOut()
    {
        if ($this->app->auth->isLoggedIn()) {
            $this->app->auth->logOut();
        }

        header('Location: /');
    }
}
