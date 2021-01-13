<?php
declare(strict_types=1);

namespace Src\Controllers;

use JetBrains\PhpStorm\NoReturn;
use Src\Core\App;
use Src\Models\Thread;
use Src\Repositories\ThreadRepository;

class ThreadController extends Controller
{
    private ThreadRepository $threadRepository;

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->threadRepository = new ThreadRepository();
    }

    public function show()
    {
        $id = (int)($_GET['id'] ?? -1);
        $thread = $this->threadRepository->getById($id);

        if (! $thread) {
            $this->threadNotFound();
        }

        $this->view->display('threads/show', [
            'thread' => $thread,
            'canEditThread' => $this->auth->canEditThread(),
        ]);
    }

    public function create(Thread $rawThread = null, $message = null)
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

        $this->view->display('threads/create', [
            'thread' => $rawThread,
            'canPinThreads' => $this->auth->canPinThreads(),
            'errorsBag' => $this->hasValidationErrors() ? $this->validator->errorsBag : $rawThread->validator->errorsBag,
            'message' => $message,
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

        $thread->validate();

        if ($thread->hasValidationErrors()) {
            $this->create($thread);
        }
        else {
            if ($this->threadRepository->insert($thread)) {
                header('Location: /threads?id=' . $thread->id);
            }
            else {
                $this->create($thread, 'Something gone wrong');
            }
        }
    }

    public function edit(Thread $thread = null, $message = null)
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
            'message' => $message,
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
