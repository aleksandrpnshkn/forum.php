<?php
declare(strict_types=1);

namespace Src\Controllers;

use JetBrains\PhpStorm\NoReturn;
use JetBrains\PhpStorm\Pure;
use Src\Core\App;
use Src\Core\Pagination;
use Src\Repositories\BoardRepository;
use Src\Repositories\ThreadRepository;

class BoardController extends Controller
{
    private BoardRepository $boardRepository;
    private ThreadRepository $threadRepository;

    #[Pure] public function __construct(App $app)
    {
        parent::__construct($app);
        $this->boardRepository = new BoardRepository();
        $this->threadRepository = new ThreadRepository();
    }

    public function show()
    {
        $slug = $_GET['slug'] ?? null;

        if (! $slug) {
            $this->boardNotFound();
        }

        $board = $this->boardRepository->getBySlug($slug);

        if (! $board) {
            $this->boardNotFound();
        }

        $threadsCount = $this->threadRepository->countWhereBoardId($board->id);
        $page = (int)($_GET['page'] ?? 1);

        $this->view->display('boards/index', [
            'board' => $board,
            'threads' => $this->threadRepository->getForPageWhereBoardId($page, $board->id),
            'pagination' => new Pagination($_SERVER['REQUEST_URI'], $threadsCount, $page),
        ]);
    }

    #[NoReturn] public function boardNotFound()
    {
        header('Location: /');
        die;
    }
}
