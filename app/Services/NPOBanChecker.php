<?php

namespace App\Services;

class NPOBanChecker extends NumberChecker
{
    protected function name(): string
    {
        return 'NPOBan';
    }

    protected function getEndpoint(): string
    {
        return 'https://www-vc.einvoice.nat.gov.tw/BIZAPIVAN/biz';
    }

    protected function validate($data): bool
    {
        if (!$data['pCode']) {
            return false;
        }
        return true;
    }

    protected function buildData(array $data): array
    {
        return array_merge([
            'version'   => '1.0',
            'action'    => 'preserveCodeCheck',
            'appId'     => $this->config('app_id'),
            'TxID'      => 'NPO'.formatDate('YmdHi').sprintf("%'06s", random_int(1, 999999)),
        ], $data);
    }
}