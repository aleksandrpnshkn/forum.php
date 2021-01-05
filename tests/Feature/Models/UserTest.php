<?php
declare(strict_types=1);

namespace Tests\Feature\Models;

use JetBrains\PhpStorm\Pure;
use Src\Models\User;
use Tests\Feature\FeatureTestCase;

class UserTest extends FeatureTestCase
{
    #[Pure] public static function makeUser() : User
    {
        $user = new User();
        $user->username = 'test';
        $user->email = 'test@example.tld';
        $user->password = 'secret';
        return $user;
    }
}
