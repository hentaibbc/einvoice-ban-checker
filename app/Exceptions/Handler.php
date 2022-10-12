<?php

namespace App\Exceptions;

use App\Loggers\Logger;
use Throwable;

class Handler
{
    private static $instance;
    private Logger $logger;

    private function __construct(Logger $logger)
    {
        $this->logger = $logger;
        $this->register();
    }

    public static function make(Logger $logger): self
    {
        if (!self::$instance) {
            self::$instance = new self($logger);
        }
        return self::$instance;
    }

    public function register(): void
    {
        set_exception_handler([$this, 'handleException']);
        set_error_handler([$this, 'handleError'], E_ALL & ~E_STRICT & ~E_NOTICE & ~E_USER_NOTICE);
    }

    public function handleException(Throwable $e): void
    {
        $message = sprintf('(%d) %s at %s line %d', $e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
        $message .= str_repeat(PHP_EOL, 2).$this->transformBacktrace($e->getTrace()).PHP_EOL;

        $this->logger->addLog($message, 'error');

        $this->response($e->getMessage());

        exit;
    }

    public function handleError(int $errno, string $errmsg, ?string $errfile = '', ?int $errline = 0, ?array $errcontext = []): void
    {
        $message = sprintf('(%d) %s at %s line %d', $errno, $errmsg, $errfile, (int) $errline);
        $message .= str_repeat(PHP_EOL, 2).$this->transformBacktrace(debug_backtrace()).PHP_EOL;

        $this->logger->addLog( $message, 'error');

        $this->response($errmsg);

        exit;
    }

    protected function response($message = ''): void
    {
        responseJson([
            'success'   => false,
            'message'   => $message,
        ], 400);
    }

    protected function transformBacktrace(array $traces): string
    {
        $messages = [];

        foreach ($traces as $index => $trace) {
            $messages[] = sprintf('#%d %s line %d.', $index, $trace['file'], (int) $trace['line']);
        }

        return implode(PHP_EOL, $messages);
    }


}