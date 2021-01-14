<?php
declare(strict_types=1);

namespace Src\Repositories;

use DateTime;
use PDO;
use PDOException;
use Src\Models\Message;
use Src\Models\Model;

/**
 * @method Message getById(int $id)
 */
class MessageRepository extends Repository implements Paginatable
{
    const TABLE_NAME = 'messages';

    public function insert(Model|Message $message) : bool
    {
        try {
            self::$db->dbh
                ->prepare('
                    INSERT INTO messages (content, author_id, thread_id)
                    VALUES (:content, :author_id, :thread_id);
                ')
                ->execute([
                    ':content' => $message->content,
                    ':author_id' => $message->author_id,
                    ':thread_id' => $message->thread_id,
                ]);

            $message->id = (int)self::$db->dbh->lastInsertId();
        } catch (PDOException $exception) {
            $this->log($exception->getMessage());
            return false;
        }

        return true;
    }

    public function update(Model|Message $message) : bool
    {
        try {
            return self::$db->dbh
                ->prepare('
                    UPDATE messages
                    SET
                        content = :content,
                        thread_id = :thread_id,
                        author_id = :author_id,
                        updated_at = :updated_at,
                        deleted_at = :deleted_at
                    WHERE id = :id;
                ')
                ->execute([
                    ':id' => $message->id,
                    ':content' => $message->content,
                    ':author_id' => $message->author_id,
                    ':thread_id' => $message->thread_id,
                    ':updated_at' => (new DateTime())->format('Y-m-d H:i:s'),
                    ':deleted_at' => $message->deleted_at?->format('Y-m-d H:i:s'),
                ]);
        } catch (PDOException $exception) {
            $this->log($exception->getMessage());
            return false;
        }
    }

    public function count() : int|false
    {
        try {
            $stmt = self::$db->dbh->query('SELECT COUNT(id) FROM messages WHERE deleted_at IS NULL');

            if ($stmt->execute()) {
                return (int)$stmt->fetch()[0];
            }
        } catch (PDOException $exception) {
            $this->log($exception->getMessage());
        }
        return false;
    }

    public function countWhereThread(int $threadId) : int|false
    {
        try {
            $stmt = self::$db
                ->dbh
                ->prepare('SELECT COUNT(id) FROM messages WHERE deleted_at IS NULL AND thread_id = :thread_id');

            $stmt->bindValue(':thread_id', $threadId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return (int)$stmt->fetch()[0];
            }
        } catch (PDOException $exception) {
            $this->log($exception->getMessage());
        }
        return false;
    }

    public function getForPage(int $page, int $perPage = 10) : array|false
    {
        try {
            $stmt = self::$db->dbh->prepare('
                SELECT * FROM messages
                WHERE deleted_at IS NULL
                ORDER BY id
                LIMIT :per_page OFFSET :offset
            ');
            $stmt->bindValue(':per_page', $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $perPage * ($page - 1), PDO::PARAM_INT);

            if ($stmt->execute()) {
                return array_map([$this, 'fillInModel'], $stmt->fetchAll());
            }
        } catch (PDOException $exception) {
            $this->log($exception->getMessage());
        }
        return false;
    }

    public function getForPageWhereThread(int $threadId, int $page, int $perPage = 10) : array|false
    {
        try {
            $stmt = self::$db->dbh->prepare('
                SELECT * FROM messages
                WHERE deleted_at IS NULL AND thread_id = :thread_id
                ORDER BY id
                LIMIT :per_page OFFSET :offset
            ');
            $stmt->bindValue(':per_page', $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $perPage * ($page - 1), PDO::PARAM_INT);
            $stmt->bindValue(':thread_id', $threadId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return array_map([$this, 'fillInModel'], $stmt->fetchAll());
            }
        } catch (PDOException $exception) {
            $this->log($exception->getMessage());
        }
        return false;
    }

    protected function fillInModel(array $data) : Message
    {
        $message = new Message();
        $message->id = (int)$data['id'];
        $message->content = $data['content'];
        $message->thread_id = (int)$data['thread_id'];
        $message->author_id = (int)$data['author_id'];
        $this->fillInTimestamps($data, $message);
        return $message;
    }
}
