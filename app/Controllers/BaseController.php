<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

/**
 * Base Controller
 *
 * All controllers extend this to gain access to the view renderer,
 * session helpers and response shortcuts.
 */
abstract class BaseController
{
    /**
     * Render a view template.
     *
     * @param string      $view   Dot-notation view path (e.g. 'home.index')
     * @param array       $data   Variables passed into the view
     * @param string|null $layout Layout to use (null = no layout)
     */
    protected function view(string $view, array $data = [], ?string $layout = 'layouts/main'): void
    {
        View::render($view, $data, $layout);
    }

    /**
     * Send a JSON response.
     */
    protected function json(mixed $data, int $status = 200): never
    {
        Response::json($data, $status);
    }

    /**
     * Redirect to a URL.
     */
    protected function redirect(string $url, int $status = 302): never
    {
        Response::redirect($url, $status);
    }

    /**
     * Redirect back to the previous page.
     */
    protected function back(): never
    {
        Response::back();
    }

    /**
     * Abort with an HTTP error.
     */
    protected function abort(int $code, string $message = ''): never
    {
        Response::abort($code, $message);
    }

    /**
     * Flash a success message into the session.
     */
    protected function withSuccess(string $message): static
    {
        Session::flash('success', $message);
        return $this;
    }

    /**
     * Flash an error message into the session.
     */
    protected function withError(string $message): static
    {
        Session::flash('error', $message);
        return $this;
    }

    /**
     * Flash validation errors into the session.
     */
    protected function withErrors(array $errors): static
    {
        Session::flash('errors', $errors);
        return $this;
    }

    /**
     * Flash old input back into the session.
     */
    protected function withInput(array $input): static
    {
        Session::flash('old', $input);
        return $this;
    }
}
