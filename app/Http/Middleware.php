<?php

namespace App\Http;

class Middleware
{
    protected Request $request;
    protected array $callables = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function register(callable $callable): self
    {
        $this->callables[] = $callable;

        return $this;
    }

    public function execute()
    {
        $request = $this->request;

        if (count($this->callables)) {
            foreach ($this->callables as $callable) {
                $next = $callable($request);
            }
        }
    }
}
