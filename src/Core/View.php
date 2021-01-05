<?php
declare(strict_types=1);

namespace Src\Core;

use Src\Core\Validation\ErrorsBag;

class View
{
    public function __construct(
        public Auth $auth
    ) {}

    public function display(string $viewName, array $vars = [])
    {
        // Add dummy to avoid Error
        if (! isset($vars['errorsBag'])) {
            $vars['errorsBag'] = new ErrorsBag();
        }

        extract($vars, EXTR_OVERWRITE);
        $_viewPath = realpath(__DIR__ . '/../../resources/views/' . rtrim(trim($viewName, '/'), '.htm') . '.htm');

        if ($_viewPath === false) {
            throw new \Exception('View template not found');
        }

        ob_start();
        require __DIR__ . '/../../resources/views/_layout.htm';
        echo ob_get_clean();
    }
}
