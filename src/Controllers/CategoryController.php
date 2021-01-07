<?php
declare(strict_types=1);

namespace Src\Controllers;

use JetBrains\PhpStorm\Pure;
use Src\Core\App;
use Src\Models\Category;
use Src\Repositories\CategoryRepository;

class CategoryController extends Controller
{
    private CategoryRepository $categoryRepository;

    #[Pure] public function __construct(App $app)
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
            'canEditCategories' => $this->canEditCategories(),
        ]);
    }

    public function create()
    {
        if (! $this->canEditCategories()) {
            $this->forbidden();
        }

        $this->view->display('categories/create', ['errorsBag' => $this->validator->errorsBag]);
    }

    public function store()
    {
        if (! $this->canEditCategories()) {
            $this->forbidden();
        }

        $category = new Category();
        $category->name = $_POST['name'] ?? null;

        if ($category->validate()) {
            if ($this->categoryRepository->insert($category)) {
                header('Location: /');
            }
            else {
                $this->view->display('categories/create', ['message' => 'Something gone wrong']);
            }
        }
        else {
            $this->view->display('categories/create', ['errorsBag' => $category->validator->errorsBag]);
        }
    }

    #[Pure] private function canEditCategories() : bool
    {
        return $this->auth->getUser()
            && $this->auth->getUser()->isAdmin();
    }
}
