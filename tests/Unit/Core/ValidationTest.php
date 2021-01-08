<?php
declare(strict_types=1);

namespace Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use Src\Core\Validation\ValidationError;
use Src\Core\Validation\ErrorsBag;
use Src\Core\Validation\Validator;

class ValidationTest extends TestCase
{
    private Validator $validator;

    public function setUp(): void
    {
        $this->validator = new Validator(new ErrorsBag());
    }

    public function testErrorsBag()
    {
        $errorsBag = new ErrorsBag();
        $this->assertFalse($errorsBag->has('name'));

        $errorsBag->add(new ValidationError('name', 'message'));
        $this->assertTrue($errorsBag->has('name'));
        $this->assertEquals('message', $errorsBag->firstMessage('name'));

        $errorsBag->add(new ValidationError('name', 'message 2'));
        $this->assertTrue($errorsBag->has('name'));
        $this->assertEquals('message', $errorsBag->firstMessage('name'));
    }

    public function testValidateRequired()
    {
        $this->validator->validateRequired('n', 'value');
        $this->assertFalse($this->validator->errorsBag->has('n'));

        $this->validator->validateRequired('n', '');
        $this->assertTrue($this->validator->errorsBag->has('n'));
    }

    public function testValidateMaxLength()
    {
        $this->validator->validateMaxLength('n', '12345', 5);
        $this->assertFalse($this->validator->errorsBag->has('n'));

        $this->validator->validateMaxLength('n', '123456', 5);
        $this->assertTrue($this->validator->errorsBag->has('n'));
    }

    public function testValidateMinLength()
    {
        $this->validator->validateMinLength('n', '123456', 6);
        $this->assertFalse($this->validator->errorsBag->has('n'));

        $this->validator->validateMinLength('n', '12345', 6);
        $this->assertTrue($this->validator->errorsBag->has('n'));
    }

    public function testValidateEmail()
    {
        $invalidEmails = [
            'email',
            'email@',
            'email@email',
            'email@email.',
        ];

        foreach ($invalidEmails as $email) {
            $validator = new Validator(new ErrorsBag());
            $validator->validateEmail('email', $email);
            $this->assertTrue($validator->errorsBag->has('email'));
        }

        $this->validator->validateEmail('email', 'email@example.tld');
        $this->assertFalse($this->validator->errorsBag->has('email'));
    }

    public function testValidateSlug()
    {
        $valid = [
            'slug',
            'slug-2',
            'slug-asd',
            '123',
        ];

        foreach ($valid as $slug) {
            $this->assertTrue($this->validator->validateSlug('n', $slug), $slug);
        }

        $invalid = [
            '',
            'slug@',
            'slug slug',
            'SLUG',
        ];

        foreach ($invalid as $slug) {
            $this->assertFalse($this->validator->validateSlug('n', $slug), $slug);
        }
    }
}
