<?php

declare(strict_types=1);

namespace App;

use App\Exceptions\ViewNotFoundException;

class View
{
    protected $view;
    protected $params = [];
    public function __construct(
        $view,
        $params = []
    ) {
        $this->view = $view;
        $this->params = $params;
    }

    public static function make(string $view, array $params = [])
    {
        return new static($view, $params);
    }

    public function withLayout(string $layout)
    {
        return new self(
            "layouts/$layout",
            ['_content' => $this] + $this->params
        );
    }

    public function render(): string
    {
        // TODO: this should be configurable
        $viewPath = __DIR__ . "/../src/Views/{$this->view}.php";

        if (!file_exists($viewPath)) {
            throw new ViewNotFoundException();
        }

        extract($this->params);

        ob_start();
        include $viewPath;
        return ob_get_clean();
    }

    public function __toString(): string
    {
        return $this->render();
    }
}
