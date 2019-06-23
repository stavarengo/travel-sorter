<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace TravelSorter\Test\Api;

use PHPUnit\Framework\TestCase;
use TravelSorter\Api\ConfigProvider;

class ConfigProviderTest extends TestCase
{
    public function testFactoryMustBeInvokable()
    {
        $configProvider = new ConfigProvider();

        $this->assertIsCallable($configProvider);
    }

    public function testInvoke()
    {
        $configProvider = new ConfigProvider();
        $config = $configProvider->__invoke();

        $expectedConfig = [];

        $this->assertJsonStringEqualsJsonString(json_encode($expectedConfig), json_encode($config));
    }


}
