<?php
declare(strict_types=1);

namespace Tests\Feature\Repositories;

use Src\Models\Category;
use Src\Repositories\CategoryRepository;
use Tests\Feature\FeatureTestCase;

class CategoryRepositoryTest extends FeatureTestCase
{
    private CategoryRepository $categoryRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->categoryRepository = new CategoryRepository();
    }

    public function testCRUD()
    {
        $this->assertNull($this->categoryRepository->getById(1));
        $category = new Category();
        $category->name = 'insert';
        $this->assertTrue($this->categoryRepository->insert($category));
        $this->assertEquals('insert', $this->categoryRepository->getById(1)->name);

        $category->name = 'update';
        $this->assertTrue($this->categoryRepository->update($category));
        $category = $this->categoryRepository->getById($category->id);
        $this->assertEquals('update', $category->name);

        $this->assertTrue($this->categoryRepository->delete($category));
        $this->assertNull($this->categoryRepository->getById($category->id));
    }

    public function testAll()
    {
        $this->assertIsArray($this->categoryRepository->getAll());

        $category1 = new Category();
        $category2 = new Category();
        $category1->name = 'category 1';
        $category2->name = 'category 2';
        $this->categoryRepository->insert($category1);
        $this->categoryRepository->insert($category2);

        $categories = $this->categoryRepository->getAll();

        $this->assertIsArray($categories);
        $this->assertCount(2, $categories);
        $this->assertContainsOnlyInstancesOf(Category::class, $categories);
    }
}
