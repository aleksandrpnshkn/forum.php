<?php
declare(strict_types=1);

namespace Src\Models;

use DateTime;
use Src\Core\Validation\ValidationError;
use Src\Repositories\MessageRepository;
use Src\Repositories\ThreadRepository;
use Src\Repositories\UserRepository;

class Message extends Model
{
    public ?string $content = null;
    public ?int $author_id = null;
    public ?int $thread_id = null;

    public ?DateTime $created_at = null;
    public ?DateTime $updated_at = null;
    public ?DateTime $deleted_at = null;

    private ?User $author = null;
    private ?Thread $thread = null;

    private MessageRepository $messageRepository;
    private UserRepository $userRepository;
    private ThreadRepository $threadRepository;

    public function __construct()
    {
        parent::__construct();
        $this->messageRepository = new MessageRepository();
        $this->userRepository = new UserRepository();
        $this->threadRepository = new ThreadRepository();
    }

    public function getAuthor() : ?User
    {
        if (! $this->author) {
            $this->author = $this->userRepository->getById($this->author_id);
        }

        return $this->author;
    }

    public function getThread() : ?Thread
    {
        if (! $this->thread) {
            $this->thread = $this->threadRepository->getById($this->thread_id);
        }

        return $this->thread;
    }

    public function isUpdated() : bool
    {
        return $this->created_at->getTimestamp() !== $this->updated_at->getTimestamp();
    }

    public function validate() : bool
    {
        $this->validator->validateRequired('content', $this->content);
        $this->validator->validateRequired('author_id', $this->author_id);
        $this->validator->validateRequired('thread_id', $this->thread_id);

        $this->validator->validateMaxLength('content', $this->content, 60000);

        if (! $this->userRepository->getById($this->author_id)) {
            $this->validator->errorsBag->add(new ValidationError('author_id', 'Author not exist'));
        }

        // Thread existence validated in controller

        return ! $this->hasValidationErrors();
    }
}
