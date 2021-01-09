<?php
declare(strict_types=1);

namespace Src\Repositories;

use DateTime;
use Exception;
use PDOException;
use Src\Models\Board;
use Src\Models\Model;

/**
 * @method Board|null getById(int $id)
 */
class BoardRepository extends Repository
{
    const TABLE_NAME = 'boards';

    public function insert(Board|Model $board) : bool
    {
        try {
            self::$db->dbh
                ->prepare('
                    INSERT INTO boards (name, slug, description, author_id, category_id)
                    VALUES (:name, :slug, :description, :author_id, :category_id);
                ')
                ->execute([
                    ':name' => $board->name,
                    ':slug' => $board->slug,
                    ':description' => $board->description,
                    ':author_id' => $board->author_id,
                    ':category_id' => $board->category_id,
                ]);

            $board->id = (int)self::$db->dbh->lastInsertId();
        } catch (PDOException $exception) {
            $this->log($exception->getMessage());
            return false;
        }

        return true;
    }

    public function update(Board|Model $board) : bool
    {
        try {
            return self::$db->dbh
                ->prepare('
                    UPDATE boards
                    SET
                        name = :name,
                        slug = :slug,
                        description = :description,
                        category_id = :category_id,
                        author_id = :author_id,
                        updated_at = :updated_at,
                        deleted_at = :deleted_at
                    WHERE id = :id;
                ')
                ->execute([
                    ':id' => $board->id,
                    ':name' => $board->name,
                    ':slug' => $board->slug,
                    ':description' => $board->description,
                    ':author_id' => $board->author_id,
                    ':category_id' => $board->category_id,
                    ':updated_at' => (new DateTime())->format('Y-m-d H:i:s'),
                    ':deleted_at' => $board->deleted_at?->format('Y-m-d H:i:s'),
                ]);
        } catch (PDOException $exception) {
            $this->log($exception->getMessage());
            return false;
        }
    }

    public function getAllWhereCategory(int $id)
    {
        return array_map([$this, 'fillInModel'], $this->getAllDataWhere('category_id', $id));
    }

    /**
     * @return Board|null
     * @throws Exception
     */
    public function getBySlug(mixed $slug) : ?Model
    {
        return $this->getBy('slug', $slug);
    }

    /**
     * @return Board
     * @throws Exception
     */
    protected function fillInModel(array $data) : Model
    {
        $board = new Board();
        $board->id = (int)$data['id'];
        $board->name = $data['name'];
        $board->slug = $data['slug'];
        $board->description = $data['description'];
        $board->category_id = $data['category_id'] ? (int)$data['category_id'] : null;
        $board->author_id = $data['author_id'] ? (int)$data['author_id'] : null;
        $this->fillInTimestamps($data, $board);
        return $board;
    }
}
