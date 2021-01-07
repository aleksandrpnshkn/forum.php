<?php
declare(strict_types=1);

namespace Src\Models;

use JetBrains\PhpStorm\Pure;
use Src\Core\Validation\ErrorsBag;
use Src\Core\Validation\Validator;

abstract class Model
{
    public Validator $validator;

    public ?int $id = null;

    #[Pure] public function __construct() {
        $this->validator = new Validator(new ErrorsBag());
    }

    abstract public function validate() : bool;

    #[Pure] public function hasValidationErrors() : bool
    {
        return $this->validator->errorsBag->hasAny();
    }
}
