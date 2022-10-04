<?php

use App\Services\BarcodeChecker;

include __DIR__.'/../../bootstrap/app.php';
include __DIR__.'/../../bootstrap/common.php';

$service = new BarcodeChecker($config['app_id'], $config['api_key']);

$resp = $service->post([
    'barCode'   => $request->input('barCode'),
]);

responseJson([
    'success'   => true,
    'data'      => [
        'exists'    => (($resp['isExist'] ?? false) == 'Y') ? true : false,
    ],
]);

exit;