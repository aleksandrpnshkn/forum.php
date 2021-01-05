<?php
declare(strict_types=1);

namespace Src\Controllers;

use JetBrains\PhpStorm\Pure;
use Src\Core\App;
use Src\Core\Validation\ErrorsBag;
use Src\Core\Validation\ValidationError;
use Src\Core\Validation\Validator;

class Controller
{
    protected App $app;

    // Yes, both Model and Controller have validators.
    // Controller is just one of validation layers before Model's validation.
    protected Validator $validator;

    #[Pure] public function __construct(App $app) {
        $this->app = $app;
        $this->validator = new Validator(new ErrorsBag());
    }

    #[Pure] protected function hasValidationErrors() : bool
    {
        return $this->validator->errorsBag->hasAny();
    }

    #[Pure] public function isPost() : bool
    {
        return strtolower($_SERVER['REQUEST_METHOD']) === 'post';
    }

    #[Pure] public function isGet() : bool
    {
        return strtolower($_SERVER['REQUEST_METHOD']) === 'get';
    }

    /**
     * Simple shortcut to add new validation error
     */
    protected function addValidationError(string $attrName, string $errorMessage) : void
    {
        $this->validator->errorsBag->add(new ValidationError($attrName, $errorMessage));
    }
}
