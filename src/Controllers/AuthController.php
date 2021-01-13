<?php
declare(strict_types=1);

namespace Src\Controllers;

use Src\Core\App;
use Src\Models\User;
use Src\Repositories\UserRepository;

class AuthController extends Controller
{
    private UserRepository $userRepository;

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->userRepository = new UserRepository();
    }

    public function registerForm()
    {
        if ($this->auth->isLoggedIn()) {
            header('Location: /');
            return;
        }

        $this->view->display('auth/register');
    }

    public function register()
    {
        $this->validateCsrfToken();

        if ($this->auth->isLoggedIn()) {
            header('Location: /');
            return;
        }

        $username = $_POST['username'] ?? null;
        $email = $_POST['email'] ?? null;
        $avatarData = $_FILES['avatar'] ?? null;
        $password = $_POST['password'] ?? null;
        $passwordConfirmation = $_POST['password_confirmation'] ?? null;
        $remember = isset($_POST['remember']) && $_POST['remember'] === 'on';

        // User's validation require hash, but before call password_hash user should provide a pass
        $this->validator->validateRequired('password', $password);

        if ($password !== $passwordConfirmation) {
            $this->addValidationError('password', 'Password not confirmed.');
        }

        // Show controller's validation errors
        if ($this->hasValidationErrors()) {
            $this->view->display('auth/register', ['errorsBag' => $this->validator->errorsBag]);
            return;
        }

        $user = new User();
        $user->username = $username;
        $user->email = $email;
        $user->password = password_hash($password, PASSWORD_DEFAULT);
        $user->avatar_path = $this->handleAvatarUpload($avatarData);
        $user->role = User::ROLE_USER;

        // Show file errors
        if ($this->hasValidationErrors()) {
            $this->view->display('auth/register', ['errorsBag' => $this->validator->errorsBag]);
            return;
        }

        if ($remember) {
            $user->remember();
            setcookie('remember_token', $user->remember_token, $user->remember_token_expires_at->getTimestamp(), '/', '', true, true);
        }

        // Show model's validation errors
        if (! $user->validate()) {
            // Delete avatar if error
            if ($user->avatar_path && realpath($user->avatar_path)) {
                unlink($user->avatar_path);
            }

            $this->view->display('auth/register', ['errorsBag' => $user->validator->errorsBag]);
            return;
        }

        if ($this->userRepository->insert($user)) {
            $this->auth->logInUser($user);
            header('Location: /');
        }
        else {
            $this->view->display('auth/register', ['message' => 'Something gone wrong']);
        }
    }

    public function logInForm()
    {
        if ($this->auth->isLoggedIn()) {
            header('Location: /');
            return;
        }

        $this->view->display('auth/login');
    }

    public function logIn()
    {
        if ($this->auth->isLoggedIn()) {
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
            $user = $this->userRepository->getByEmail($email);

            if ($user && $this->verifyPassword($password, $user->password)) {
                $this->auth->logInUser($user);

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
            $this->view->display('auth/login', ['errorsBag' => $this->validator->errorsBag]);
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
        if ($this->auth->isLoggedIn()) {
            $this->auth->logOut();
        }

        header('Location: /');
    }

    private function handleAvatarUpload(array $avatarData) : ?string
    {
        if ($avatarData['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if (
            $avatarData['size'] > 1024*1024*1
            || $avatarData['error'] === UPLOAD_ERR_INI_SIZE
            || $avatarData['error'] === UPLOAD_ERR_FORM_SIZE
        ) {
            $this->addValidationError('avatar', 'Image is too big');
            return null;
        }

        if ($avatarData['error'] !== UPLOAD_ERR_OK) {
            $this->addValidationError('avatar', 'Error occurred on file uploading');
            return null;
        }

        $allowedMimes = ['image/png', 'image/jpeg'];
        if (! in_array(mime_content_type($avatarData['tmp_name']), $allowedMimes, true)) {
            $this->addValidationError('avatar', 'Image should have png or jpg extension');
            return null;
        }

        $avatarPath = 'avatars/' . uniqid('', true);

        if (move_uploaded_file($avatarData['tmp_name'], self::$uploadsDirPath . "/$avatarPath")) {
            return $avatarPath;
        }
        else {
            $this->addValidationError('avatar', 'Error occurred on file uploading');
            return null;
        }
    }
}
