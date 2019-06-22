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

        $mockContainerGetResultMap = [];

        if ($doesContainerHasConfigEntry) {
            $mockContainerGetResultMap[] = ['config', $config];
        } else {
            $stubContainer->method('get')->willThrowException(new \Exception());
        }

        if (!$isContainerWellConfigured) {
            $this->expectException(MissingConfigEntry::class);
        }

        if ($isContainerWellConfigured) {
            foreach ($config[ConfigProvider::class][DispatcherInterface::class][DispatcherInterface::CONFIG_DISPATCHERS] as $expectedDispatcher) {
                $mockContainerGetResultMap[] = [$expectedDispatcher, $this->createMock(DispatcherInterface::class)];
            }
        }

        if ($mockContainerGetResultMap) {
            $stubContainer->method('get')->willReturnMap($mockContainerGetResultMap);
        }

        $mockBasePathDetector = $this->createMock(BasePathDetectorInterface::class);
        $dispatcherAggregate = $factory->__invoke($mockBasePathDetector, $stubContainer);

        $basePathDetectorConfig = $config[ConfigProvider::class][DispatcherInterface::class];
        $expectedDispatchers = $basePathDetectorConfig[DispatcherInterface::CONFIG_DISPATCHERS];

        $this->assertInstanceOf(DispatcherAggregate::class, $dispatcherAggregate);
        $this->assertSame($mockBasePathDetector, $dispatcherAggregate->getBasePathDetector());
        $this->assertCount(count($expectedDispatchers), $dispatcherAggregate->getDispatchers());
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
                                'DispatchService1',
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
                                'DispatchService1',
                                'DispatchService2',
                                'DispatchService3',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
