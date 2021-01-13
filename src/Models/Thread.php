<?php
declare(strict_types=1);

namespace Src\Models;

use DateTime;
use JetBrains\PhpStorm\Pure;
use Src\Core\Validation\ValidationError;
use Src\Repositories\BoardRepository;
use Src\Repositories\UserRepository;

class Thread extends Model
{
    const STATUS_OPEN = 'Open';
    const STATUS_CLOSED = 'Closed';

    public ?string $name = null;
    public ?string $status = self::STATUS_OPEN;
    public bool $is_pinned = false;
    public ?int $board_id = null;
    public ?int $author_id = null;

    public ?DateTime $created_at = null;
    public ?DateTime $updated_at = null;
    public ?DateTime $deleted_at = null;

    private UserRepository $userRepository;
    private BoardRepository $boardRepository;

    #[Pure] public function __construct()
    {
        parent::__construct();
        $this->userRepository = new UserRepository();
        $this->boardRepository = new BoardRepository();
    }

    public function getAuthor() : ?User
    {
        if (! $this->author_id) {
            return null;
        }

        return $this->userRepository->getById($this->author_id);
    }

    public function getBoard() : ?Board
    {
        if (! $this->board_id) {
            return null;
        }

        return $this->boardRepository->getById($this->board_id);
    }

    public function isOpen() : bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function isClosed() : bool
    {
        return $this->status === self::STATUS_CLOSED;
    }

    public function validate() : bool
    {
        $this->validator->validateRequired('name', $this->name);
        $this->validator->validateMinLength('name', $this->name ?? '', 3);
        $this->validator->validateMaxLength('name', $this->name ?? '', 100);

        $this->validator->validateRequired('board', $this->board_id);

        // check if board exists
        if (! $this->boardRepository->getById($this->board_id)) {
            $this->validator->errorsBag->add(new ValidationError('board_id', 'Board not exist'));
        }

        // author should be checked in controller, because moderator should have rights to update theme if author deleted

        if (! in_array($this->status, [self::STATUS_CLOSED, self::STATUS_OPEN])) {
            $this->validator->errorsBag->add(new ValidationError('status', 'Thread has wrong status'));
        }

        return ! $this->hasValidationErrors();
    }
}
