<?php

namespace App\Loggers;

interface Logger
{
    public function addLog(string $message, ?string $type = null): void;
}