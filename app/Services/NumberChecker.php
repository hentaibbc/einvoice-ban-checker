<?php

namespace App\Services;

use App\Loggers\Logger;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

abstract class NumberChecker
{
    protected array $confs = [];
    protected ?Logger $logger = null;

    public function __construct($app_id, $api_key, ?Logger $logger = null)
    {
        $this->confs = [
            'app_id'    => $app_id,
            'api_key'   => $api_key,
        ];

        $this->logger = $logger;
    }

    protected function config($key)
    {
        if (!isset($this->confs[$key])) {
            throw new RuntimeException(sprintf('Config index "%s" is not exists', $key));
        }

        return $this->confs[$key] ?? null;
    }

    public function post(array $data)
    {
        if (!$this->validate($data)) {
            throw new InvalidArgumentException('Invalid arguments');
        }

        $api_key = $this->config('api_key');

        $data = $this->buildData($data);
        // dd($data);
        $query = buildQuery($data);

        $sig = hash_hmac('sha256', $query, $api_key);
        $sig = urlencode(base64_encode($sig));

        $url = $this->getEndpoint();
        $query .= '&signature='.$sig;

        $this->log('Request: '.$url);
        $this->log('Data: '.$query);

        $curl = curl_init($url);

        curl_setopt_array($curl, [
            CURLOPT_SSL_VERIFYHOST => '0',
            CURLOPT_SSL_VERIFYPEER => '0',
            CURLOPT_POST           => '1',
            CURLOPT_POSTFIELDS     => $query,
            CURLOPT_HEADER         => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_VERBOSE        => false,
        ]);

        $body = curl_exec($curl);

        $this->log('Response: '.$body);

        return json_decode($body, true);
    }

    protected function log($message): void
    {
        if (! ($this->logger instanceof Logger)) {
            return;
        }
        $this->logger->addLog($message, $this->name());
    }

    abstract protected function name(): string;
    abstract protected function getEndpoint(): string;
    abstract protected function validate(array $data): bool;
    abstract protected function buildData(array $data): array;
}