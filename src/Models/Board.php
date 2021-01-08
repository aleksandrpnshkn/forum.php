<?php
declare(strict_types=1);

namespace Src\Models;

use DateTime;
use JetBrains\PhpStorm\Pure;
use Src\Core\Validation\ValidationError;
use Src\Repositories\BoardRepository;
use Src\Repositories\CategoryRepository;
use Src\Repositories\UserRepository;

class Board extends Model
{
    private BoardRepository $boardRepository;

    public ?string $name = null;
    public ?string $slug = null;
    public ?string $description = null;
    public ?int $category_id = null;
    public ?int $author_id = null;
    public ?DateTime $created_at = null;
    public ?DateTime $updated_at = null;
    public ?DateTime $deleted_at = null;

    private CategoryRepository $categoryRepository;
    private UserRepository $userRepository;

    #[Pure] public function __construct()
    {
        parent::__construct();
        $this->boardRepository = new BoardRepository();
        $this->categoryRepository = new CategoryRepository();
        $this->userRepository = new UserRepository();
    }

    public function validate(): bool
    {
        $this->validator->validateRequired('name', $this->name);
        $this->validator->validateRequired('slug', $this->slug);
        $this->validator->validateRequired('category', $this->category_id);
        $this->validator->validateRequired('author_id', $this->author_id);

        if ($this->hasValidationErrors()) {
            return false;
        }

        $this->validator->validateMinLength('name', $this->name ?? '', 3);
        $this->validator->validateMaxLength('name', $this->name ?? '', 100);

        $this->validator->validateSlug('slug', $this->slug);

        $this->validator->validateMaxLength('description', $this->description, 255);

        if (! $this->categoryRepository->getById($this->category_id)) {
            $this->validator->errorsBag->add(new ValidationError('category', 'Category not exists'));
        }

        if (! $this->userRepository->getById($this->author_id)) {
            $this->validator->errorsBag->add(new ValidationError('author', 'User not exists'));
        }

        return ! $this->hasValidationErrors();
    }
}
