<?php
declare(strict_types=1);

namespace Tests\Feature\Models;

use JetBrains\PhpStorm\Pure;
use Src\Models\User;
use Tests\Feature\FeatureTestCase;

class UserTest extends FeatureTestCase
{
    public function testRemember()
    {
        $user = self::makeUser();
        $user->remember();
        $this->assertIsString($user->remember_token);
        $this->assertInstanceOf(\DateTime::class, $user->remember_token_expires_at);
    }

    public function testValidate()
    {
        $user = new User();
        $user->username = 'WRONG ;';
        $this->assertFalse($user->validate());
        $this->assertTrue($user->hasValidationErrors());
        $this->assertTrue($user->validator->errorsBag->has('username'));
        $this->assertTrue($user->validator->errorsBag->has('email'));
        $this->assertTrue($user->validator->errorsBag->has('password'));
    }

    #[Pure] public static function makeUser() : User
    {
        $user = new User();
        $user->username = 'test';
        $user->email = 'test@example.tld';
        $user->password = 'secret';
        return $user;
    }
}
