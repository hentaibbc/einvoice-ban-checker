<?php

use App\Services\BarcodeChecker;

include __DIR__.'/../../bootstrap/app.php';
include __DIR__.'/../../bootstrap/common.php';

$service = new BarcodeChecker($config['app_id'], $config['api_key'], ($config['curl_log_enable'] ? $logger : null));

$resp = $service->post([
    'barCode'   => $request->input('barCode'),
]);

if (($resp['code'] ?? false) != 200) {
    throw new RuntimeException('Response code error');
}

responseJson([
    'success'   => true,
    'data'      => [
        'exists'    => (($resp['isExist'] ?? false) == 'Y') ? true : false,
    ],
]);

exit;