<?php
declare(strict_types=1);

namespace Src\Repositories;

use DateTime;
use PDO;
use PDOException;
use Src\Models\Model;
use Src\Models\Thread;

/**
 * @method Thread|null getById(int $id)
 */
class ThreadRepository extends Repository implements Paginatable
{
    const TABLE_NAME = 'threads';

    /**
     * @return Thread[]|false
     */
    public function getForPage(int $page, int $perPage = 10) : array|false
    {
        try {
            $stmt = self::$db->dbh->prepare('
                SELECT * FROM threads
                WHERE deleted_at IS NULL
                ORDER BY is_pinned DESC, updated_at DESC
                LIMIT :perPage OFFSET :offset
            ');
            $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $perPage * ($page - 1), PDO::PARAM_INT);

            if ($stmt->execute()) {
                return array_map([$this, 'fillInModel'], $stmt->fetchAll());
            }
        } catch (PDOException $exception) {
            $this->log($exception->getMessage());
        }
        return false;
    }

    public function getForPageWhereBoardId(int $page, int $boardId, int $perPage = 10) : array|false
    {
        try {
            $stmt = self::$db->dbh->prepare('
                SELECT * FROM threads
                WHERE board_id=:id AND deleted_at IS NULL
                ORDER BY is_pinned DESC, updated_at DESC
                LIMIT :perPage OFFSET :offset
            ');
            $stmt->bindValue(':id', $boardId, PDO::PARAM_INT);
            $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $perPage * ($page - 1), PDO::PARAM_INT);

            if ($stmt->execute()) {
                return array_map([$this, 'fillInModel'], $stmt->fetchAll());
            }
        } catch (PDOException $exception) {
            $this->log($exception->getMessage());
        }
        return false;
    }

    public function count() : int|false
    {
        try {
            $stmt = self::$db->dbh->query('SELECT COUNT(id) FROM threads WHERE deleted_at IS NULL');

            if ($stmt->execute()) {
                return (int)$stmt->fetch()[0];
            }
        } catch (PDOException $exception) {
            $this->log($exception->getMessage());
        }
        return false;
    }

    public function countWhereBoardId(int $id) : int|false
    {
        try {
            $stmt = self::$db
                ->dbh
                ->prepare('SELECT COUNT(id) FROM threads WHERE board_id=:board_id AND deleted_at IS NULL');
            $stmt->bindValue(':board_id', $id);

            if ($stmt->execute()) {
                return (int)$stmt->fetch()[0];
            }
        } catch (PDOException $exception) {
            $this->log($exception->getMessage());
        }
        return false;
    }

    public function insert(Model|Thread $thread) : bool
    {
        try {
            $stmt = self::$db->dbh->prepare('
                INSERT INTO threads (name, status, is_pinned, board_id, author_id)
                VALUES (:name, :status, :is_pinned, :board_id, :author_id);
            ');

            $stmt->bindValue(':name', $thread->name);
            $stmt->bindValue(':status', $thread->status);
            $stmt->bindValue(':is_pinned', $thread->is_pinned, PDO::PARAM_BOOL);
            $stmt->bindValue(':board_id', $thread->board_id);
            $stmt->bindValue(':author_id', $thread->author_id);

            $stmt->execute();

            $thread->id = (int)self::$db->dbh->lastInsertId();
        } catch (PDOException $exception) {
            $this->log($exception->getMessage());
            return false;
        }

        return true;
    }

    public function update(Model|Thread $thread) : bool
    {
        try {
            $stmt = self::$db->dbh->prepare('
                UPDATE threads
                SET
                    name = :name,
                    status = :status,
                    is_pinned = :is_pinned,
                    board_id = :board_id,
                    author_id = :author_id,
                    updated_at = :updated_at,
                    deleted_at = :deleted_at
                WHERE id = :id;
            ');

            $stmt->bindValue(':id', $thread->id);
            $stmt->bindValue(':name', $thread->name);
            $stmt->bindValue(':status', $thread->status);
            $stmt->bindValue(':is_pinned', $thread->is_pinned, PDO::PARAM_BOOL);
            $stmt->bindValue(':board_id', $thread->board_id);
            $stmt->bindValue(':author_id', $thread->author_id);
            $stmt->bindValue(':updated_at', (new DateTime())->format('Y-m-d H:i:s'));
            $stmt->bindValue(':deleted_at', $thread->deleted_at?->format('Y-m-d H:i:s'));

            return $stmt->execute();
        } catch (PDOException $exception) {
            $this->log($exception->getMessage());
            return false;
        }
    }

    protected function fillInModel(array $data) : Thread
    {
        $thread = new Thread();
        $thread->id = (int)$data['id'];
        $thread->name = $data['name'];
        $thread->status = $data['status'];
        $thread->is_pinned = (bool)$data['is_pinned'];
        $thread->author_id = $data['author_id'] ? (int)$data['author_id'] : null;
        $thread->board_id = $data['board_id'] ? (int)$data['board_id'] : null;
        $this->fillInTimestamps($data, $thread);
        return $thread;
    }
}
