<?php
declare(strict_types=1);

namespace Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use Src\Core\Pagination;

class PaginationTest extends TestCase
{
    public function testBuild()
    {
        $pagination = new Pagination('https://example.com/boards', 70, 1);
        $this->assertEquals([
            '1' => null,
            '2' => '/boards?page=2',
            '3' => '/boards?page=3',
            '7' => '/boards?page=7',
            'prev' => null,
            'next' => '/boards?page=2',
            'first' => '/boards?page=1',
            'last' => '/boards?page=7',
        ], $pagination->build());

        $pagination = new Pagination('https://example.com/boards', 70, 2);
        $this->assertEquals([
            '1' => '/boards?page=1',
            '2' => null,
            '3' => '/boards?page=3',
            '7' => '/boards?page=7',
            'prev' => '/boards?page=1',
            'next' => '/boards?page=3',
            'first' => '/boards?page=1',
            'last' => '/boards?page=7',
        ], $pagination->build());

        $pagination = new Pagination('https://example.com/boards', 70, 4);
        $this->assertEquals([
            '1' => '/boards?page=1',
            '3' => '/boards?page=3',
            '4' => null,
            '5' => '/boards?page=5',
            '7' => '/boards?page=7',
            'prev' => '/boards?page=3',
            'next' => '/boards?page=5',
            'first' => '/boards?page=1',
            'last' => '/boards?page=7',
        ], $pagination->build());

        $pagination = new Pagination('https://example.com/boards', 70, 6);
        $this->assertEquals([
            '1' => '/boards?page=1',
            '5' => '/boards?page=5',
            '6' => null,
            '7' => '/boards?page=7',
            'prev' => '/boards?page=5',
            'next' => '/boards?page=7',
            'first' => '/boards?page=1',
            'last' => '/boards?page=7',
        ], $pagination->build());

        $pagination = new Pagination('https://example.com/boards', 70, 7);
        $this->assertEquals([
            '1' => '/boards?page=1',
            '5' => '/boards?page=5',
            '6' => '/boards?page=6',
            '7' => null,
            'prev' => '/boards?page=6',
            'next' => null,
            'first' => '/boards?page=1',
            'last' => '/boards?page=7',
        ], $pagination->build());

        $pagination = new Pagination('https://example.com/boards', 20, 1);
        $this->assertEquals([
            '1' => null,
            '2' => '/boards?page=2',
            'prev' => null,
            'next' => '/boards?page=2',
            'first' => '/boards?page=1',
            'last' => '/boards?page=2',
        ], $pagination->build(), 'When less than length');

        $pagination = new Pagination('https://example.com/boards', 10, 1);
        $this->assertEquals([
            '1' => null,
            'prev' => null,
            'next' => null,
            'first' => null,
            'last' => null,
        ], $pagination->build(), 'When less than length');
    }
}
