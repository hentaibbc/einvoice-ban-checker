<?php declare(strict_types=1);

namespace App\Tests\Unit;

use App\Services\NPOBanChecker;
use PHPUnit\Framework\TestCase;

final class NPOBanTest extends TestCase
{
    public function getConfig()
    {
        global $config;
        return $config;
    }

    public function codeProvider(): array
    {
        return [
            ['1995', 'Y'],
            ['1991', 'N'],
        ];
    }

    /**
     * @dataProvider codeProvider
     */
    public function testSuccess($code, $except)
    {
        $config = $this->getConfig();

        $service = new NPOBanChecker($config['app_id'], $config['api_key']);
        $resp = $service->post([
            'pCode'   => $code,
        ]);

        $this->assertSame($except, $resp['isExist'] ?? false);
    }
}