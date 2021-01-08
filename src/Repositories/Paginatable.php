<?php
declare(strict_types=1);

namespace Src\Repositories;

interface Paginatable
{
    public function count() : int|false;

    public function getForPage(int $page, int $perPage = 10) : array|false;
}
