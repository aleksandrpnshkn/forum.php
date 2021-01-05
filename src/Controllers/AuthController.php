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
}
