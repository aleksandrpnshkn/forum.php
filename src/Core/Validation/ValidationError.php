<?php
declare(strict_types=1);

namespace Src\Core\Validation;

class ValidationError
{
    public string $name;
    public string $message;

    public function __construct(string $name, string $message)
    {
        $this->name = $name;
        $this->message = $message;
    }
}
