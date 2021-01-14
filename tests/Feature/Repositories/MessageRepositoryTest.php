<?php
declare(strict_types=1);

namespace Tests\Feature\Repositories;

use Src\Models\Message;
use Src\Repositories\MessageRepository;
use Src\Repositories\ThreadRepository;
use Tests\Feature\FeatureTestCase;

class MessageRepositoryTest extends FeatureTestCase
{
    private MessageRepository $messageRepository;

    public function setUp() : void
    {
        parent::setUp();
        $this->messageRepository = new MessageRepository();
    }

    public function testCRUD()
    {
        $this->assertNull($this->messageRepository->getById(1));
        $message = $this->makeMessage('insert');
        $this->assertTrue($this->messageRepository->insert($message));
        $this->assertEquals('insert', $this->messageRepository->getById(1)->content);

        $message->content = 'update';
        $this->assertTrue($this->messageRepository->update($message));
        $message = $this->messageRepository->getById($message->id);
        $this->assertEquals('update', $message->content);

        $this->assertTrue($this->messageRepository->delete($message));
        $this->assertNull($this->messageRepository->getById($message->id));
    }

    public function testCount()
    {
        $this->assertEquals(0, $this->messageRepository->count());

        for ($i = 0; $i < 13; $i++) {
            $this->assertTrue($this->messageRepository->insert($this->makeMessage()));
        }

        $this->assertEquals(13, $this->messageRepository->count());
    }

    public function testGetForPage()
    {
        for ($i = 0; $i < 3; $i++) {
            $this->assertTrue($this->messageRepository->insert($this->makeMessage()));
        }
        $threadsForPage1 = $this->messageRepository->getForPage(1, 2);
        $this->assertCount(2, $threadsForPage1);
        $threadsForPage2 = $this->messageRepository->getForPage(2, 2);
        $this->assertCount(1, $threadsForPage2);
    }

    private function makeMessage(string $content = null) : Message
    {
        $threadRepository = new ThreadRepository();
        $thread = ThreadRepositoryTest::makeThread();
        $threadRepository->insert($thread);

        $message = new Message();
        $message->content = $content ?? uniqid('content', true);
        $message->thread_id = $thread->id;

        return $message;
    }
}
