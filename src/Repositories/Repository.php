<?php
declare(strict_types=1);

namespace Src\Repositories;

use DateTime;
use Exception;
use Src\Core\App;
use Src\Core\Database;
use Src\Models\Model;

abstract class Repository
{
    protected const TABLE_NAME = null;

    public static Database $db;

    abstract public function insert(Model $model) : bool;

    abstract public function update(Model $model) : bool;

    public function delete(Model $model) : bool
    {
        if (! static::TABLE_NAME) {
            throw new Exception('Table name not set');
        }

        return self::$db->dbh
            ->prepare('DELETE FROM ' . static::TABLE_NAME . ' WHERE id = :id')
            ->execute([':id' => $model->id]);
    }

    public function getById(int $id) : ?Model
    {
        return $this->getBy('id', $id);
    }

    /**
     * Only $value can be provided by user!
     */
    protected function getBy(string $name, mixed $value) : ?Model
    {
        if (! static::TABLE_NAME) {
            throw new Exception('Table name not set');
        }

        if (! preg_match('/^[a-z_]+$/', $name)) {
            throw new \InvalidArgumentException('Column name contains bad chars');
        }

        $stmt = self::$db->dbh->prepare('SELECT * FROM ' . static::TABLE_NAME . " WHERE $name = :$name");
        $stmt->execute([":$name" => $value]);
        $data = $stmt->fetch();
        return $data
            ? $this->fillInModel($data)
            : null;
    }

    protected function getAllData() : array
    {
        if (! static::TABLE_NAME) {
            throw new Exception('Table name not set');
        }

        $stmt = self::$db->dbh->prepare('SELECT * FROM ' . static::TABLE_NAME . ' WHERE deleted_at IS NULL');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    abstract protected function fillInModel(array $data) : Model;

    protected function fillInTimestamps(array $data, Model $model) : Model
    {
        $model->created_at = $data['created_at']
            ? new DateTime($data['created_at'])
            : null;
        $model->updated_at = $data['updated_at']
            ? new DateTime($data['updated_at'])
            : null;
        $model->deleted_at = $data['deleted_at']
            ? new DateTime($data['deleted_at'])
            : null;
        return $model;
    }

    protected function log(string $message) : bool
    {
        return error_log($message . "\n", LOG_ERR, App::ERROR_LOG);
    }
}
