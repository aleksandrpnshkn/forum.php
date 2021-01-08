<?php
declare(strict_types=1);

namespace Src\Core\Validation;

class Validator
{
    public ErrorsBag $errorsBag;

    public function __construct(ErrorsBag $errorsBag)
    {
        $this->errorsBag = $errorsBag;
    }

    public function validateRequired(string $name, mixed $value) : bool
    {
        if (empty($value)) {
            $errorMessage = mb_convert_case($name, MB_CASE_TITLE) . ' is required.';
            $this->errorsBag->add(new ValidationError($name, $errorMessage));
            return false;
        }
        return true;
    }

    public function validateMaxLength(string $name, string $value, int $length) : bool
    {
        if (mb_strlen($value) > $length) {
            $errorMessage = mb_convert_case($name, MB_CASE_TITLE) . " has length more than $length.";
            $this->errorsBag->add(new ValidationError($name, $errorMessage));
            return false;
        }
        return true;
    }

    public function validateMinLength(string $name, string $value, int $length) : bool
    {
        if (mb_strlen($value) < $length) {
            $errorMessage = mb_convert_case($name, MB_CASE_TITLE) . " has length less than $length.";
            $this->errorsBag->add(new ValidationError($name, $errorMessage));
            return false;
        }
        return true;
    }

    public function validateEmail(string $name, string $value) : bool
    {
        if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $errorMessage = mb_convert_case($name, MB_CASE_TITLE) . ' is not valid email address.';
            $this->errorsBag->add(new ValidationError($name, $errorMessage));
            return false;
        }
        return true;
    }

    public function validateSlug(string $name, string $slug) : bool
    {
        if (! preg_match('/^[\da-z\-]+$/', $slug)) {
            $this->errorsBag->add(new ValidationError($name, 'Slug is not valid.'));
            return false;
        }
        return true;
    }
}
