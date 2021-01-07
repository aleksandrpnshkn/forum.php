<?php
declare(strict_types=1);

namespace Src\Models;

use DateTime;
use JetBrains\PhpStorm\Pure;
use Src\Core\Validation\ValidationError;
use Src\Repositories\CategoryRepository;

class Category extends Model
{
    private CategoryRepository $categoryRepository;

    public ?string $name = null;
    public ?DateTime $created_at = null;
    public ?DateTime $updated_at = null;
    public ?DateTime $deleted_at = null;

    #[Pure] public function __construct()
    {
        parent::__construct();
        $this->categoryRepository = new CategoryRepository();
    }

    public function validate(): bool
    {
        $this->validator->validateRequired('name', $this->name);
        $this->validator->validateMinLength('name', $this->name, 3);
        $this->validator->validateMaxLength('name', $this->name, 100);

        // Check unique name
        if ($this->categoryRepository->getByName($this->name)) {
            $this->validator->errorsBag->add(new ValidationError('name', 'Category name must be unique'));
        }

        return ! $this->hasValidationErrors();
    }
}
