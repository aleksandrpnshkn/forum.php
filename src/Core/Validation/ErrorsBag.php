<?php
declare(strict_types=1);

namespace Src\Core\Validation;

use JetBrains\PhpStorm\Pure;

class ErrorsBag
{
    /** @var ValidationError[] */
    private array $errors = [];

    public function add(ValidationError $error) : void
    {
        $this->errors[] = $error;
    }

    public function first(string $name) : ?ValidationError
    {
        foreach ($this->errors as $error) {
            if ($error->name === $name) {
                return $error;
            }
        }

        return null;
    }

    #[Pure] public function firstMessage(string $name) : ?string
    {
        return $this->first($name)?->message;
    }

    #[Pure] public function has(string $name) : bool
    {
        return (bool)$this->first($name);
    }

    #[Pure] public function hasAny() : bool
    {
        return count($this->errors) > 0;
    }
}
