<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace TravelSorter\Test\App\Dispatcher;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use TravelSorter\App\BasePathDetector\BasePathDetectorInterface;
use TravelSorter\App\ConfigProvider;
use TravelSorter\App\Dispatcher\DispatcherAggregate;
use TravelSorter\App\Dispatcher\DispatcherAggregateFactory;
use TravelSorter\App\Dispatcher\DispatcherInterface;
use TravelSorter\App\Dispatcher\Exception\MissingConfigEntry;

class DispatcherAggregateFactoryTest extends TestCase
{
    public function testFactoryMustBeInvokable()
    {
        $factory = new DispatcherAggregateFactory();

        $this->assertIsCallable($factory);
    }

    /**
     * @dataProvider containerIsMissConfiguredProvider
     */
    public function testFactoryMustGetTheConfigFromTheContainer(bool $isContainerWellConfigured, ?array $config)
    {
        $factory = new DispatcherAggregateFactory();

        /** @var ContainerInterface|\PHPUnit\Framework\MockObject\MockObject $stubContainer */
        $stubContainer = $this->createMock(ContainerInterface::class);

        $doesContainerHasConfigEntry = $config !== null;

        $stubContainer->method('has')->willReturnMap([['config', $doesContainerHasConfigEntry]]);

        if ($doesContainerHasConfigEntry) {
            $stubContainer->method('get')->willReturnMap([['config', $config]]);
        } else {
            $stubContainer->method('get')->willThrowException(new \Exception());
        }

        if (!$isContainerWellConfigured) {
            $this->expectException(MissingConfigEntry::class);
        }

        $mockBasePathDetector = $this->createMock(BasePathDetectorInterface::class);
        $dispatcher = $factory->__invoke($mockBasePathDetector, $stubContainer);

        $basePathDetectorConfig = $config[ConfigProvider::class][DispatcherInterface::class];
        $expectedDispatchers = $basePathDetectorConfig[DispatcherInterface::CONFIG_DISPATCHERS];

        $this->assertInstanceOf(DispatcherAggregate::class, $dispatcher);
        $this->assertSame($mockBasePathDetector, $dispatcher->getBasePathDetector());
        $this->assertSame($expectedDispatchers, $dispatcher->getDispatchers());
        $this->assertCount(count($expectedDispatchers), $dispatcher->getDispatchers());
    }

    public function containerIsMissConfiguredProvider(): array
    {
        return [
            [false, null],
            [false, []],
            [false, [ConfigProvider::class => []]],
            [
                false,
                [
                    ConfigProvider::class => [
                        DispatcherInterface::class => [],
                    ],
                ],
            ],
            [
                true,
                [
                    ConfigProvider::class => [
                        DispatcherInterface::class => [
                            DispatcherInterface::CONFIG_DISPATCHERS => [
                                $this->createMock(DispatcherInterface::class)
                            ],
                        ],
                    ],
                ],
            ],
            [
                true,
                [
                    ConfigProvider::class => [
                        DispatcherInterface::class => [
                            DispatcherInterface::CONFIG_DISPATCHERS => [
                                $this->createMock(DispatcherInterface::class),
                                $this->createMock(DispatcherInterface::class),
                                $this->createMock(DispatcherInterface::class),
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
