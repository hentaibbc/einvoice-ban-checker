<?php

namespace App\Loggers;

class FileLogger implements Logger
{
    private array $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function addLog($message, $type = null): void
    {
        $folder = $type;
        $daily_folder = $this->logPath($folder);

        if (!file_exists($daily_folder)) {
            mkdir($daily_folder, 0777, true);
        } else {
            if (!is_dir($daily_folder)) {
                responseJson([
                    'success' => false,
                    'message' => 'Log path is not a directory'
                ], 400);
            }
        }

        $file = ($this->config['log_prefix'] ?? 'log').'_'.formatDate('Ymd').'.log';
        $fp = fopen($daily_folder.'/'.$file, 'a+');
        if ($fp) {
            fputs($fp, sprintf('%s %s'.PHP_EOL, formatDate('Y-m-d H:i:s'), $message));
            fclose($fp);
        }
    }

    protected function logPath(string $file): string
    {
        return rtrim($this->config['log_path'], '/').'/'.ltrim($file, '/');
    }
}