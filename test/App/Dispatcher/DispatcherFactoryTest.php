<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace TravelSorter\Test\App\Dispatcher;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use TravelSorter\App\ConfigProvider;
use TravelSorter\App\Dispatcher\Dispatcher;
use TravelSorter\App\Dispatcher\DispatcherFactory;
use TravelSorter\App\Dispatcher\DispatcherInterface;
use TravelSorter\App\Dispatcher\Exception\MissingConfigEntry;
use TravelSorter\App\RouteDetector\RouteDetectorInterface;
use Zend\Stdlib\RequestInterface;

class DispatcherFactoryTest extends TestCase
{
    public function testFactoryMustBeInvokable()
    {
        $factory = new DispatcherFactory();

        $this->assertIsCallable($factory);
    }

    /**
     * @dataProvider containerIsMissConfiguredProvider
     */
    public function testFactoryMustGetTheConfigFromTheContainer(bool $isContainerWellConfigured, ?array $config)
    {
        $factory = new DispatcherFactory();

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
            foreach ($config[ConfigProvider::class][DispatcherInterface::class][DispatcherInterface::REQUEST_HANDLER_MAP] as $routeMap) {
                foreach ($routeMap as $requestHandlerServiceName) {
                    $mockContainerGetResultMap[] = [
                        $requestHandlerServiceName,
                        $this->createMock(RequestInterface::class)
                    ];
                }
            }
        }

        if ($mockContainerGetResultMap) {
            $stubContainer->method('get')->willReturnMap($mockContainerGetResultMap);
        }

        $mockRouteDetector = $this->createMock(RouteDetectorInterface::class);
        $dispatcher = $factory->__invoke($mockRouteDetector, $stubContainer);

        $basePathDetectorConfig = $config[ConfigProvider::class][DispatcherInterface::class];
        $expectedDispatchers = $basePathDetectorConfig[DispatcherInterface::REQUEST_HANDLER_MAP];

        $this->assertInstanceOf(Dispatcher::class, $dispatcher);
        $this->assertSame($mockRouteDetector, $dispatcher->getRouteDetector());
        $this->assertCount(count($expectedDispatchers), $dispatcher->getRequestHandlerMap());
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
                            DispatcherInterface::REQUEST_HANDLER_MAP => [
                                'route1' => [
                                    'POST' => 'RequestHandlerServiceNameService1',
                                ]
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
                            DispatcherInterface::REQUEST_HANDLER_MAP => [
                                'route1' => [
                                    'POST' => 'RequestHandlerServiceNameService1',
                                    'GET' => 'RequestHandlerServiceNameService2',
                                ],
                                'route2' => [
                                    'PUT' => 'RequestHandlerServiceNameService3',
                                ]
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
