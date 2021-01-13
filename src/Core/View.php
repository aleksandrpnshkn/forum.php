<?php
declare(strict_types=1);

namespace Src\Core;

use JetBrains\PhpStorm\Pure;
use Src\Core\Validation\ErrorsBag;

class View
{
    private Csrf $csrf;

    public function __construct(
        public Auth $auth
    ) {
        $this->csrf = new Csrf();
    }

    public function display(string $viewName, array $vars = [])
    {
        // Add dummy to avoid Error
        if (! isset($vars['errorsBag'])) {
            $vars['errorsBag'] = new ErrorsBag();
        }

        $vars['csrfField'] = $this->getCsrfField();

        extract($vars, EXTR_OVERWRITE);

        $viewName = trim($viewName, '/');
        if (! str_ends_with($viewName, '.htm')) {
            $viewName .= '.htm';
        }
        $_viewPath = realpath(__DIR__ . '/../../resources/views/' . $viewName);

        if ($_viewPath === false) {
            throw new \Exception('View template not found');
        }

        ob_start();
        require __DIR__ . '/../../resources/views/_layout.htm';
        echo ob_get_clean();
    }

    #[Pure] protected function getCsrfField() : string
    {
        return '<input type="hidden" name="_csrf" value="' . $this->csrf->getToken() . '">';
    }
}
