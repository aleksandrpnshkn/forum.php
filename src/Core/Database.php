<?php
declare(strict_types=1);

namespace Src\Core;

use PDO;

final class Database
{
    public PDO $dbh;

    public function __construct()
    {
        $host = getenv('MYSQL_HOST');
        $dbname = getenv('MYSQL_DATABASE');
        $user = getenv('MYSQL_USER');
        $password = getenv('MYSQL_PASSWORD');

        $this->dbh = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
        $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
}