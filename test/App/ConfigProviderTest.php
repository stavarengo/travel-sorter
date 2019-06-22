<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace TravelSorter\Test\App;

use PHPUnit\Framework\TestCase;
use TravelSorter\App\BasePathDetector\BasePathDetectorInterface;
use TravelSorter\App\ConfigProvider;
use TravelSorter\App\Dispatcher\DispatcherAggregate;
use TravelSorter\App\Dispatcher\DispatcherInterface;
use TravelSorter\App\RouteDetector\RouteDetectorInterface;
use TravelSorter\App\TicketsSorter\TicketsSorterInterface;

class ConfigProviderTest extends TestCase
{
    public function testFactoryMustBeInvokable()
    {
        $configProvider = new ConfigProvider();

        $this->assertIsCallable($configProvider);
    }

    public function testContainerDefinitions()
    {
        $configProvider = new ConfigProvider();

        $config = $configProvider->getContainerDefinitions();

        $this->assertCount(4, $config);
        $this->assertArrayHasKey(BasePathDetectorInterface::class, $config);
        $this->assertArrayHasKey(DispatcherInterface::class, $config);
        $this->assertArrayHasKey(TicketsSorterInterface::class, $config);
        $this->assertArrayHasKey(RouteDetectorInterface::class, $config);
    }

    public function testBasePathDetectorConfig()
    {
        $configProvider = new ConfigProvider();

        $config = $configProvider->getBasePathDetectorConfig();

        $this->assertCount(2, $config);
        $this->assertSame(
            $config[BasePathDetectorInterface::CONFIG_PUBLIC_DIRECTORY],
            realpath(__DIR__ . '/../../public')
        );
        $this->assertSame(
            $config[BasePathDetectorInterface::CONFIG_DOCUMENT_ROOT],
            $_SERVER['DOCUMENT_ROOT']
        );
    }

    public function testDispatcherAggregateConfig()
    {
        $configProvider = new ConfigProvider();

        $config = $configProvider->getDispatcherAggregateConfig();

        $this->assertCount(1, $config);
        $this->assertArrayHasKey(DispatcherAggregate::CONFIG_DISPATCHERS, $config);
        $this->assertCount(0, $config[DispatcherAggregate::CONFIG_DISPATCHERS]);
    }

    public function testInvoke()
    {
        $configProvider = new ConfigProvider();
        $config = $configProvider->__invoke();

        $expectedConfig = [
            'container_definitions' => $configProvider->getContainerDefinitions(),
            ConfigProvider::class => [
                BasePathDetectorInterface::class => $configProvider->getBasePathDetectorConfig(),
                DispatcherAggregate::class => $configProvider->getDispatcherAggregateConfig(),
            ]
        ];

        $this->assertJsonStringEqualsJsonString(json_encode($expectedConfig), json_encode($config));
    }


}
