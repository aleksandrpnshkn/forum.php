<?php
declare(strict_types=1);

namespace Tests\Feature\Repositories;

use Src\Repositories\UserRepository;
use Tests\Feature\FeatureTestCase;
use Tests\Feature\Models\UserTest;

class UserRepositoryTest extends FeatureTestCase
{
    private UserRepository $userRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->getApp()->getUserRepository();
    }

    public function testInsert()
    {
        $this->assertNull($this->userRepository->getById(1));
        $user = UserTest::makeUser();
        $this->assertTrue($this->userRepository->insert($user));

        $this->assertEquals('test', $this->userRepository->getById(1)->username);
    }

    public function testUpdate()
    {
        $user = UserTest::makeUser();
        $this->userRepository->insert($user);
        $user->username = 'updated';
        $this->assertTrue($this->userRepository->update($user));
        $this->assertEquals('updated', $user->username);
    }

    public function testDelete()
    {
        $user = UserTest::makeUser();
        $this->userRepository->insert($user);
        $this->assertTrue($this->userRepository->delete($user->id));
        $this->assertNull($this->userRepository->getById($user->id));
    }
}
