<?php
declare(strict_types=1);

namespace Src\Controllers;

use Src\Core\App;
use Src\Models\Message;
use Src\Models\Thread;
use Src\Repositories\MessageRepository;
use Src\Repositories\Repository;
use Src\Repositories\ThreadRepository;

class MessageController extends Controller
{
    private MessageRepository $messageRepository;
    private ThreadRepository $threadRepository;

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->messageRepository = new MessageRepository();
        $this->threadRepository = new ThreadRepository();
    }

    public function create(Message $rawMessage = null, Thread $thread = null, $appMessage = null)
    {
        if (! $thread) {
            $threadId = (int)($_GET['thread_id'] ?? null);
            $thread = $this->threadRepository->getById($threadId);
        }

        if (! $thread || ! $this->auth->canReply($thread)) {
            $this->forbidden();
        }

        // Create a dummy to keep filled in data if there was a validation error
        if (! $rawMessage) {
            $rawMessage = new Message();
            $rawMessage->thread_id = $thread->id;
        }

        $this->view->display('messages/form', [
            'thread' => $thread,
            'message' => $rawMessage,
            'canPinThreads' => $this->auth->canPinThreads(),
            'actionUrl' => '/messages/create?thread_id=' . $thread->id,
            'errorsBag' => $this->hasValidationErrors() ? $this->validator->errorsBag : $rawMessage->validator->errorsBag,
            'appMessage' => $appMessage,
        ]);
    }

    public function store()
    {
        $this->validateCsrfToken();

        $threadId = (int)($_GET['thread_id'] ?? null);
        $thread = $this->threadRepository->getById($threadId);

        if (! $thread || ! $this->auth->canReply($thread)) {
            $this->forbidden();
        }

        $message = new Message();
        $message->content = (string)($_POST['content'] ?? null);
        $message->author_id = $this->auth->getUser()->id;
        $message->thread_id = $thread->id;

        $message->validate();

        if ($message->hasValidationErrors()) {
            $this->create($message, $thread);
            return;
        }

        Repository::$db->dbh->beginTransaction();

        if (
            $this->messageRepository->insert($message)
            && $this->threadRepository->update($thread) // Indicate that the thread was updated
            && Repository::$db->dbh->commit()
        ) {
            header('Location: /threads?id=' . $message->thread_id);
            return;
        }

        Repository::$db->dbh->rollBack();
        $this->create($message, $thread, 'Something gone wrong');
    }

    public function edit(Message $message = null, $appMessage = null)
    {
        // Create a dummy to keep filled in data if there was a validation error
        if (! $message) {
            $id = (int)($_GET['id'] ?? null);
            $message = $this->messageRepository->getById($id);
        }

        if (! $message) {
            $this->forbidden();
        }

        $this->view->display('messages/form', [
            'thread' => $message->getThread(),
            'message' => $message,
            'canPinThreads' => $this->auth->canPinThreads(),
            'actionUrl' => '/messages/update?id=' . $message->id,
            'errorsBag' => $this->hasValidationErrors() ? $this->validator->errorsBag : $message->validator->errorsBag,
            'appMessage' => $appMessage,
        ]);
    }

    public function update()
    {
        $this->validateCsrfToken();

        $id = (int)($_GET['id'] ?? null);
        $message = $this->messageRepository->getById($id);

        if (! $message) {
            $this->forbidden();
        }

        $message->content = (string)($_POST['content'] ?? null);

        $message->validate();

        if ($message->hasValidationErrors()) {
            $this->create($message);
            return;
        }

        if ($this->messageRepository->update($message)) {
            header('Location: /threads?id=' . $message->thread_id);
            return;
        }

        $this->edit($message, 'Something gone wrong');
    }
}
