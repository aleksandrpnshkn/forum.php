<?php
declare(strict_types=1);

namespace Src\Controllers;

use JetBrains\PhpStorm\NoReturn;
use Src\Core\App;
use Src\Core\Pagination;
use Src\Models\Message;
use Src\Models\Thread;
use Src\Repositories\MessageRepository;
use Src\Repositories\Repository;
use Src\Repositories\ThreadRepository;

class ThreadController extends Controller
{
    private ThreadRepository $threadRepository;
    private MessageRepository $messageRepository;

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->threadRepository = new ThreadRepository();
        $this->messageRepository = new MessageRepository();
    }

    public function show()
    {
        $id = (int)($_GET['id'] ?? -1);
        $thread = $this->threadRepository->getById($id);

        if (! $thread) {
            $this->threadNotFound();
        }

        $page = (int)($_GET['page'] ?? 1);
        $messages = $this->messageRepository->getForPageWhereThread($thread->id, $page);
        $messagesCount = $this->messageRepository->countWhereThread($thread->id);

        $this->view->display('threads/show', [
            'thread' => $thread,
            'messages' => $messages,
            'pagination' => new Pagination('/threads?id=' . $thread->id, $messagesCount, $page),
            'canEditThread' => $this->auth->canEditThread(),
            'canReply' => $this->auth->canReply($thread),
        ]);
    }

    public function create(Thread $rawThread = null, Message $rawMessage = null, $appMessage = null)
    {
        if (! $this->auth->canCreateThread()) {
            $this->forbidden();
        }

        // Create a dummy to keep filled in data if there was a validation error
        if (! $rawThread) {
            $rawThread = new Thread();
            $rawThread->board_id = (int)($_GET['board_id'] ?? 0);

            if (! $rawThread->getBoard()) {
                $this->forbidden();
            }
        }

        if (! $rawMessage) {
            $rawMessage = new Message();
        }

        if ($this->hasValidationErrors()) {
            $errorsBag = $this->validator->errorsBag;
        }
        elseif ($rawThread->hasValidationErrors()) {
            $errorsBag = $rawThread->validator->errorsBag;
        }
        else {
            $errorsBag = $rawMessage->validator->errorsBag;
        }

        $this->view->display('threads/create', [
            'thread' => $rawThread,
            'message' => $rawMessage,
            'canPinThreads' => $this->auth->canPinThreads(),
            'errorsBag' => $errorsBag,
            'appMessage' => $appMessage,
        ]);
    }

    public function store()
    {
        $this->validateCsrfToken();

        if (! $this->auth->canCreateThread()) {
            $this->forbidden();
        }

        $thread = new Thread();
        $thread->name = (string)($_POST['name'] ?? '');
        $thread->board_id = (int)($_GET['board_id'] ?? null);
        $thread->author_id = $this->auth->getUser()->id;
        $thread->status = Thread::STATUS_OPEN;
        $thread->is_pinned = $this->auth->canPinThreads()
            ? filter_input(INPUT_POST, 'is_pinned') === 'on'
            : false;

        // message data should also be kept if there will be validation errors
        // , so create root message with user data before thread validation
        $message = new Message();
        $message->content = (string)($_POST['content'] ?? null);
        $message->author_id = $this->auth->getUser()->id;

        $thread->validate();

        if ($thread->hasValidationErrors()) {
            $this->create($thread, $message);
            return;
        }

        Repository::$db->dbh->beginTransaction();

        if ($this->threadRepository->insert($thread)) {
            $message->thread_id = $thread->id;
            $message->validate();

            if ($message->hasValidationErrors()) {
                Repository::$db->dbh->rollBack();
                $this->create($thread, $message);
                return;
            }

            if (
                $this->messageRepository->insert($message)
                && Repository::$db->dbh->commit()
            ) {
                header('Location: /threads?id=' . $thread->id);
                return;
            }
        }

        Repository::$db->dbh->rollBack();
        $this->create($thread, $message, 'Something gone wrong');
    }

    public function edit(Thread $thread = null, $appMessage = null)
    {
        if (! $thread) {
            $id = (int)($_GET['id'] ?? null);
            $thread = $this->threadRepository->getById($id);

            if (! $thread) {
                $this->threadNotFound();
            }
        }

        if (! $this->auth->canEditThread($thread)) {
            $this->forbidden();
        }

        $this->view->display('threads/update', [
            'thread' => $thread,
            'canPinThreads' => $this->auth->canPinThreads(),
            'errorsBag' => $this->hasValidationErrors() ? $this->validator->errorsBag : $thread->validator->errorsBag,
            'appMessage' => $appMessage,
        ]);
    }

    public function update()
    {
        $this->validateCsrfToken();

        $id = (int)($_GET['id'] ?? null);
        $thread = $this->threadRepository->getById($id);

        if (! $thread) {
            $this->threadNotFound();
        }

        if (! $this->auth->canEditThread($thread)) {
            $this->forbidden();
        }

        $thread->name = (string)($_POST['name'] ?? '');
        $is_closed = ($_POST['is_closed'] ?? '') === 'on';

        // Author can only close the thread, but not open
        if ($is_closed) {
            $thread->status = Thread::STATUS_CLOSED;
        }

        $thread->is_pinned = $this->auth->canPinThreads()
            ? ($_POST['is_pinned'] ?? null) === 'on'
            : false;

        if (! $thread->validate()) {
            $this->edit($thread);
            return;
        }

        if ($this->threadRepository->update($thread)) {
            $this->edit($thread, 'Thread successfully updated!');
        }
        else {
            $this->edit($thread, 'Something gone wrong');
        }
    }

    #[NoReturn] protected function threadNotFound()
    {
        http_response_code(404);
        $this->view->display('threads/404');
        die;
    }
}
