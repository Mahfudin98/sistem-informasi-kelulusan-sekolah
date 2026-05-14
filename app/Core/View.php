<?php

declare(strict_types=1);

namespace App\Core;

/**
 * View Renderer
 *
 * Renders PHP view templates with layout support.
 * Views live in app/Views/.
 */
final class View
{
    private string $layout = 'layouts/main';
    private array  $data   = [];

    /**
     * Render a view with optional layout.
     *
     * @param string $view  Dot-notation path (e.g. 'home.index')
     * @param array  $data  Variables to extract into the view
     */
    public static function render(string $view, array $data = [], ?string $layout = 'layouts/main'): void
    {
        $instance = new self();
        $instance->layout = $layout ?? '';
        $instance->data   = $data;
        $instance->display($view);
    }

    /**
     * Render a partial without any layout.
     */
    public static function partial(string $view, array $data = []): void
    {
        self::render($view, $data, null);
    }

    /**
     * Return rendered view HTML as a string.
     */
    public static function make(string $view, array $data = []): string
    {
        ob_start();
        self::render($view, $data, null);
        return ob_get_clean() ?: '';
    }

    // ── Internal ──────────────────────────────────────────────────────────────

    private function display(string $view): void
    {
        $viewFile = $this->resolveViewPath($view);

        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View [{$view}] not found at: {$viewFile}");
        }

        // Capture view content
        extract($this->data, EXTR_SKIP);
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Render inside layout if specified
        if ($this->layout !== '') {
            $layoutFile = $this->resolveViewPath($this->layout);

            if (!file_exists($layoutFile)) {
                throw new \RuntimeException("Layout [{$this->layout}] not found.");
            }

            require $layoutFile;
        } else {
            echo $content;
        }
    }

    private function resolveViewPath(string $view): string
    {
        // Convert dot notation to directory separators
        $relativePath = str_replace('.', DIRECTORY_SEPARATOR, $view) . '.php';
        return VIEW_PATH . DIRECTORY_SEPARATOR . $relativePath;
    }
}
