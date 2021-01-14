<?php
declare(strict_types=1);

namespace Tests\Feature\Repositories;

use Src\Models\Thread;
use Src\Repositories\ThreadRepository;
use Tests\Feature\FeatureTestCase;

class ThreadRepositoryTest extends FeatureTestCase
{
    private ThreadRepository $threadRepository;

    public function setUp() : void
    {
        parent::setUp();
        $this->threadRepository = new ThreadRepository();
    }

    public function testCRUD()
    {
        $this->assertNull($this->threadRepository->getById(1));
        $thread = $this->makeThread('insert');
        $this->assertTrue($this->threadRepository->insert($thread));
        $this->assertEquals('insert', $this->threadRepository->getById(1)->name);

        $thread->name = 'update';
        $this->assertTrue($this->threadRepository->update($thread));
        $thread = $this->threadRepository->getById($thread->id);
        $this->assertEquals('update', $thread->name);

        $this->assertTrue($this->threadRepository->delete($thread));
        $this->assertNull($this->threadRepository->getById($thread->id));
    }

    public function testCount()
    {
        $this->assertEquals(0, $this->threadRepository->count());

        for ($i = 0; $i < 13; $i++) {
            $this->assertTrue($this->threadRepository->insert(self::makeThread()));
        }

        $this->assertEquals(13, $this->threadRepository->count());
    }

    public function testGetForPage()
    {
        for ($i = 0; $i < 3; $i++) {
            $this->assertTrue($this->threadRepository->insert(self::makeThread()));
        }
        $threadsForPage1 = $this->threadRepository->getForPage(1, 2);
        $this->assertCount(2, $threadsForPage1);
        $threadsForPage2 = $this->threadRepository->getForPage(2, 2);
        $this->assertCount(1, $threadsForPage2);
    }

    public static function makeThread(string $name = null) : Thread
    {
        $thread = new Thread();
        $thread->name = $name ?? uniqid();
        $thread->is_pinned = (bool)random_int(0, 1);
        return $thread;
    }
}
