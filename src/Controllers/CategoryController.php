<?php
declare(strict_types=1);

namespace Src\Controllers;

use Src\Core\App;
use Src\Models\Category;
use Src\Repositories\CategoryRepository;

class CategoryController extends Controller
{
    private CategoryRepository $categoryRepository;

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->categoryRepository = new CategoryRepository();
    }

    public function index()
    {
        $this->view->display('home', [
            'username' => $this->auth->isLoggedIn()
                ? $this->auth->getUser()->username
                : 'Guest',
            'categories' => $this->categoryRepository->getAll(),
            'canEditCategories' => $this->auth->canEditCategories(),
            'canEditBoards' => $this->auth->canEditBoards(),
        ]);
    }

    public function create()
    {
        if (! $this->auth->canEditCategories()) {
            $this->forbidden();
        }

        $this->view->display('categories/create', ['errorsBag' => $this->validator->errorsBag]);
    }

    public function store()
    {
        $this->validateCsrfToken();

        if (! $this->auth->canEditCategories()) {
            $this->forbidden();
        }

        $category = new Category();
        $category->name = $_POST['name'] ?? null;

        if ($category->validate()) {
            if ($this->categoryRepository->insert($category)) {
                header('Location: /');
            }
            else {
                $this->view->display('categories/create', ['appMessage' => 'Something gone wrong']);
            }
        }
        else {
            $this->view->display('categories/create', ['errorsBag' => $category->validator->errorsBag]);
        }
    }

    public function edit()
    {
        if (! $this->auth->canEditCategories()) {
            $this->forbidden();
        }

        $id = (int)($_GET['id'] ?? null);

        if (! $id) {
            header('Location: /');
            die;
        }

        $category = $this->categoryRepository->getById($id);

        $this->view->display('categories/update', [
            'errorsBag' => $category->validator->errorsBag,
            'category' => $category,
        ]);
    }

    public function update()
    {
        $this->validateCsrfToken();

        if (! $this->auth->canEditCategories()) {
            $this->forbidden();
        }

        $id = (int)($_GET['id'] ?? null);
        $appMessage = null;

        if (! $id) {
            header('Location: /');
            die;
        }

        $category = $this->categoryRepository->getById($id);
        $category->id = $id;
        $category->name = $_POST['name'] ?? '';

        if ($category->validate()) {
            $appMessage = $this->categoryRepository->update($category)
                ? 'Category successfully updated'
                : 'Something gone wrong';
        }

        $this->view->display('categories/update', [
            'errorsBag' => $category->validator->errorsBag,
            'category' => $category,
            'appMessage' => $appMessage,
        ]);
    }
}
