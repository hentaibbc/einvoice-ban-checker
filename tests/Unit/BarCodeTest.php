<?php declare(strict_types=1);

namespace App\Tests\Unit;

use App\Services\BarcodeChecker;
use PHPUnit\Framework\TestCase;

final class BarCodeTest extends TestCase
{
    public function getConfig()
    {
        global $config;
        return $config;
    }

    public function barcodeProvider(): array
    {
        return [
            ['/FSKS4O2', 'Y'],
            ['/FSKS400', 'N'],
        ];
    }

    /**
     * @dataProvider barcodeProvider
     */
    public function testSuccess($code, $except)
    {
        $config = $this->getConfig();

        $service = new BarcodeChecker($config['app_id'], $config['api_key']);
        $resp = $service->post([
            'barCode'   => $code,
        ]);

        $this->assertSame($except, $resp['isExist'] ?? false);
    }
}