<?php
declare(strict_types=1);

namespace Tests\Feature\Repositories;

use JetBrains\PhpStorm\Pure;
use Src\Models\Board;
use Src\Repositories\BoardRepository;
use Tests\Feature\FeatureTestCase;

class BoardRepositoryTest extends FeatureTestCase
{
    private BoardRepository $boardRepository;

    public function setUp() : void
    {
        parent::setUp();
        $this->boardRepository = new BoardRepository();
    }

    public function testCRUD()
    {
        $this->assertNull($this->boardRepository->getById(1));
        $board = $this->makeBoard('insert');
        $this->assertTrue($this->boardRepository->insert($board));
        $this->assertEquals('insert', $this->boardRepository->getById(1)->name);

        $board->name = 'update';
        $this->assertTrue($this->boardRepository->update($board));
        $board = $this->boardRepository->getById($board->id);
        $this->assertEquals('update', $board->name);

        $this->assertTrue($this->boardRepository->delete($board));
        $this->assertNull($this->boardRepository->getById($board->id));
    }

    #[Pure] private function makeBoard(string $name = null) : Board
    {
        $board = new Board();
        $board->name = $name ?? uniqid();
        $board->slug = uniqid();
        return $board;
    }
}
