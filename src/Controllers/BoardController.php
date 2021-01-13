<?php
declare(strict_types=1);

namespace Src\Controllers;

use JetBrains\PhpStorm\NoReturn;
use Src\Core\App;
use Src\Core\Pagination;
use Src\Models\Board;
use Src\Repositories\BoardRepository;
use Src\Repositories\CategoryRepository;
use Src\Repositories\ThreadRepository;

class BoardController extends Controller
{
    private BoardRepository $boardRepository;
    private ThreadRepository $threadRepository;
    private CategoryRepository $categoryRepository;

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->boardRepository = new BoardRepository();
        $this->threadRepository = new ThreadRepository();
        $this->categoryRepository = new CategoryRepository();
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

        $this->view->display('boards/show', [
            'board' => $board,
            'canCreateThreads' => $this->auth->canPinThreads(),
            'threads' => $this->threadRepository->getForPageWhereBoardId($page, $board->id),
            'pagination' => new Pagination($_SERVER['REQUEST_URI'], $threadsCount, $page),
        ]);
    }

    public function create(Board $rawBoard = null, $message = null)
    {
        if (! $this->auth->canEditBoards()) {
            $this->forbidden();
        }

        // Create a dummy to keep filled in data if there was a validation error
        if (! $rawBoard) {
            $rawBoard = new Board();
        }

        $this->view->display('boards/create', [
            'board' => $rawBoard,
            'categories' => $this->categoryRepository->getAll(),
            'errorsBag' => $rawBoard->validator->errorsBag,
            'message' => $message,
        ]);
    }

    public function store()
    {
        $this->validateCsrfToken();

        if (! $this->auth->canEditBoards()) {
            $this->forbidden();
        }

        // Create a dummy to keep filled in data if there was a validation error
        $board = new Board();
        $this->fillBoardFromPost($board);
        $board->author_id = $this->auth->getUser()->id;

        $board->validate();

        if ($board->hasValidationErrors()) {
            $this->create($board);
        }
        else {
            if ($this->boardRepository->insert($board)) {
                header('Location: /');
            }
            else {
                $this->create($board, 'Something gone wrong');
            }
        }
    }

    public function edit(Board $board = null, $message = null)
    {
        if (! $board) {
            $id = (int)($_GET['id'] ?? -1);
            $board = $this->boardRepository->getById($id);

            if (! $board) {
                $this->boardNotFound();
            }
        }

        $this->view->display('boards/update', [
            'board' => $board,
            'categories' => $this->categoryRepository->getAll(),
            'errorsBag' => $this->hasValidationErrors() ? $this->validator->errorsBag : $board->validator->errorsBag,
            'message' => $message,
        ]);
    }

    public function update()
    {
        $this->validateCsrfToken();

        $id = (int)($_GET['id'] ?? -1);
        $board = $this->boardRepository->getById($id);

        if (! $board) {
            $this->boardNotFound();
        }

        $this->fillBoardFromPost($board);

        if (! $board->validate()) {
            $this->edit($board);
            return;
        }

        if ($this->boardRepository->update($board)) {
            $this->edit($board, 'Board successfully updated!');
        }
        else {
            $this->edit($board, 'Something gone wrong');
        }
    }

    private function fillBoardFromPost(Board $board)
    {
        $board->name = $_POST['name'] ?? null;
        $board->slug = $_POST['slug'] ?? null;
        $board->description = $_POST['description'] ?? null;
        $board->category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    }

    #[NoReturn] public function boardNotFound()
    {
        header('Location: /');
        die;
    }
}
