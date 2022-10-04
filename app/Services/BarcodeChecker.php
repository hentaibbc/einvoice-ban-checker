<?php

namespace App\Services;

class BarcodeChecker extends NumberChecker
{
    protected function getEndpoint(): string
    {
        return 'https://www-vc.einvoice.nat.gov.tw/BIZAPIVAN/biz';
    }

    protected function validate($data): bool
    {
        if (!$data['barCode']) {
            return false;
        }
        return true;
    }

    protected function buildData(array $data): array
    {
        return array_merge([
            'version'   => '1.0',
            'action'    => 'bcv',
            'appId'     => $this->config('app_id'),
            'TxID'      => 'BC'.formatDate('YmdHi').sprintf("%'06s", random_int(1, 999999)),
        ], $data);
    }
}