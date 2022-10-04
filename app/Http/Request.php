<?php

namespace App\Http;

use RuntimeException;
use Throwable;

class Request
{
    protected array $sources = [];
    protected string $contentType = 'field';
    protected array $data;

    public function __construct()
    {
        $this->sources = [
            'post'    => $_POST,
            'get'     => $_GET,
            'input'   => $_REQUEST,
            'header'  => $this->parseHeaders(),
            'raw'     => file_get_contents('php://input'),
        ];

        $this->contentType = $this->parseContentType();
        $this->parseInput();
    }

    protected function parseContentType()
    {
        if (isset($_SERVER['HTTP_CONTENT_TYPE'])) {
            if (preg_match('#^application/json#i', trim($_SERVER['HTTP_CONTENT_TYPE']))) {
                return 'json';
            }
        }

        return 'field';
    }

    protected function parseHeaders()
    {
        $headers = [];
        foreach ($_SERVER as $key => $val)
        {
            if (preg_match('#^HTTP_#', $key)) {
                $hkey = substr($key, 5);
                $headers[$hkey] = $val;
            }
        }

        return $headers;
    }

    public function isJson()
    {
        return $this->contentType == 'json';
    }

    public function parseInput()
    {
        if ($this->isJson()) {
            $data = json_decode($this->sources['raw'], true);
            if (json_last_error()) {
                throw new RuntimeException('JSON decode failed: '.json_last_error_msg());
            }

            $this->sources['post'] = $data;
            $this->sources['input'] = array_merge($this->sources['input'], $data);
        }
    }

    public function header($key)
    {
        return $this->sources['header'][$key] ?? null;
    }

    public function post(string $key, $default = null)
    {
        return $this->sources['post'][$key] ?? $default;
    }

    public function get(string $key, $default = null)
    {
        return $this->sources['get'][$key] ?? $default;
    }

    public function input(string $key, $default = null)
    {
        return $this->sources['input'][$key] ?? $default;
    }

    public function all(): array
    {
        return $this->sources['input'] ?? [];
    }

    public function raw(): string
    {
        return $this->sources['raw'] ?? '';
    }
}