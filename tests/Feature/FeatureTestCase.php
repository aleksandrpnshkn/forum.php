<?php
declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use Src\Core\App;

require_once __DIR__ . '/../../vendor/autoload.php';

class FeatureTestCase extends TestCase
{
    private static App $app;
    private static string $migrateSql;
    private static string $rollbackSql;

    public static function setUpBeforeClass(): void
    {
        self::$app = new App();

        self::$migrateSql = file_get_contents(self::$app->tablesPath);
        self::$rollbackSql = file_get_contents(self::$app->rollbackPath);
    }

    public function setUp(): void
    {
        self::$app->db->dbh->exec(self::$migrateSql);
    }

    public function tearDown(): void
    {
        self::$app->db->dbh->exec(self::$rollbackSql);
    }

    protected function getApp() : App
    {
        return self::$app;
    }
}
