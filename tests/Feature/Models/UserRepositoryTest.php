<?php
declare(strict_types=1);

namespace Tests\Feature\Models;

use Src\Models\User;
use Src\Repositories\UserRepository;
use Tests\Feature\FeatureTest;

class UserRepositoryTest extends FeatureTest
{
    private UserRepository $userRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->getApp()->userRepository;
    }

    public function testInsert()
    {
        $this->assertFalse($this->userRepository->getById(1));
        $user = new User([
            'username' => 'test',
            'password' => 'secret',
        ]);
        $this->assertTrue($this->userRepository->insert($user));
        $this->assertEquals('test', $this->userRepository->getById(1)->username);
    }

    public function testUpdate()
    {
        $user = new User([
            'username' => 'test',
            'password' => 'secret',
        ]);
        $this->userRepository->insert($user);
        $user->username = 'updated';
        $this->assertTrue($this->userRepository->update($user));
        $this->assertEquals('updated', $user->username);
    }

    public function testDelete()
    {
        $user = new User([
            'username' => 'test',
            'password' => 'secret',
        ]);
        $this->getApp()->userRepository->insert($user);
        $this->assertTrue($this->getApp()->userRepository->delete($user->id));
        $this->assertFalse($this->getApp()->userRepository->getById($user->id));
    }
}
