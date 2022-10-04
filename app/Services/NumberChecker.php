<?php

namespace App\Services;

use InvalidArgumentException;
use RuntimeException;
use Throwable;

abstract class NumberChecker
{
    protected array $confs = [];

    public function __construct($app_id, $api_key)
    {
        $this->confs = [
            'app_id'    => $app_id,
            'api_key'   => $api_key,
        ];
    }

    protected function config($key)
    {
        if (!isset($this->confs[$key])) {
            throw new RuntimeException(sprintf('Config index "%s" is not exists', $key));
        }

        return $this->confs[$key];
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

        // dd($query.'&signature = '.$sig);

        $curl = curl_init($this->getEndpoint());

        curl_setopt_array($curl, [
            CURLOPT_SSL_VERIFYHOST => '0',
            CURLOPT_SSL_VERIFYPEER => '0',
            CURLOPT_POST           => '1',
            CURLOPT_POSTFIELDS     => $query.'&signature='.$sig,
            CURLOPT_HEADER         => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_VERBOSE        => false,
        ]);

        $body = curl_exec($curl);

        return json_decode($body, true);
    }

    abstract protected function getEndpoint(): string;
    abstract protected function validate(array $data): bool;
    abstract protected function buildData(array $data): array;
}