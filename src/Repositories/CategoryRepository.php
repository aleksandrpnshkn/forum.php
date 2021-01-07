<?php
declare(strict_types=1);

namespace Src\Repositories;

use DateTime;
use Exception;
use PDOException;
use Src\Core\App;
use Src\Models\Model;
use Src\Models\Category;

/**
 * @method Category|null getById(int $id)
 */
class CategoryRepository extends Repository
{
    protected const TABLE_NAME = 'categories';

    /**
     * @return Category|null
     * @throws Exception
     */
    public function getByName(string $name) : ?Model
    {
        return $this->getBy('name', $name);
    }

    public function insert(Model|Category $category): bool
    {
        try {
            self::$db->dbh
                ->prepare('
                    INSERT INTO categories (name)
                    VALUES (:name);
                ')
                ->execute([
                    ':name' => $category->name,
                ]);

            $category->id = (int)self::$db->dbh->lastInsertId();
        } catch (PDOException $exception) {
            error_log($exception->getMessage() . "\n", LOG_ERR, App::ERROR_LOG);
            return false;
        }

        return true;
    }

    public function update(Model|Category $category): bool
    {
        try {
            return self::$db->dbh
                ->prepare('
                    UPDATE categories
                    SET
                        name = :name,
                        updated_at = :updated_at,
                        deleted_at = :deleted_at
                    WHERE id = :id;
                ')
                ->execute([
                    ':id' => $category->id,
                    ':name' => $category->name,
                    ':updated_at' => (new DateTime())->format('Y-m-d H:i:s'),
                    ':deleted_at' => $category->deleted_at?->format('Y-m-d H:i:s'),
                ]);
        } catch (PDOException $exception) {
            error_log($exception->getMessage() . "\n", LOG_ERR, App::ERROR_LOG);
            return false;
        }
    }

    /**
     * @return Category[]
     * @throws Exception
     */
    public function getAll() : array
    {
        return array_map([$this, 'fillInModel'], $this->getAllData());
    }

    protected function fillInModel(array $data): Category
    {
        $section = new Category();
        $section->id = (int)$data['id'];
        $section->name = $data['name'];
        $this->fillInTimestamps($data, $section);
        return $section;
    }
}
