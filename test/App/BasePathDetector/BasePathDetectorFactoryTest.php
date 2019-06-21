<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace TravelSorter\Test\App\BasePathDetector;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use TravelSorter\App\BasePathDetector\BasePathDetector;
use TravelSorter\App\BasePathDetector\BasePathDetectorFactory;
use TravelSorter\App\BasePathDetector\BasePathDetectorInterface;
use TravelSorter\App\BasePathDetector\Exception\MissingConfigEntry;
use TravelSorter\App\ConfigProvider;

class BasePathDetectorFactoryTest extends TestCase
{
    public function testFactoryMustBeInvokable()
    {
        $basePathDetectorFactory = new BasePathDetectorFactory();

        $this->assertIsCallable($basePathDetectorFactory);
    }

    /**
     * @dataProvider containerIsMissConfiguredProvider
     */
    public function testFactoryMustGetTheConfigFromTheContainer(bool $isContainerWellConfigured, ?array $config)
    {
        $basePathDetectorFactory = new BasePathDetectorFactory();

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

        $basePathDetector = $basePathDetectorFactory->__invoke($stubContainer);

        $basePathDetectorConfig = $config[ConfigProvider::class][BasePathDetectorInterface::class];
        $expectedDocumentRoot = $basePathDetectorConfig[BasePathDetectorInterface::CONFIG_DOCUMENT_ROOT];
        $expectedPublicDirectory = $basePathDetectorConfig[BasePathDetectorInterface::CONFIG_PUBLIC_DIRECTORY];

        $this->assertInstanceOf(BasePathDetector::class, $basePathDetector);
        $this->assertEquals($basePathDetector->getDocumentRoot(), $expectedDocumentRoot);
        $this->assertEquals($basePathDetector->getPublicDirectoryPath(), $expectedPublicDirectory);
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
                        BasePathDetectorInterface::class => [
                            BasePathDetectorInterface::CONFIG_PUBLIC_DIRECTORY => '/',
                        ],
                    ],
                ],
            ],
            [
                false,
                [
                    ConfigProvider::class => [
                        BasePathDetectorInterface::class => [
                            BasePathDetectorInterface::CONFIG_DOCUMENT_ROOT => '/var/www/html',
                        ],
                    ],
                ],
            ],
            [
                true,
                [
                    ConfigProvider::class => [
                        BasePathDetectorInterface::class => [
                            BasePathDetectorInterface::CONFIG_PUBLIC_DIRECTORY => '/',
                            BasePathDetectorInterface::CONFIG_DOCUMENT_ROOT => '/var/www/html',
                        ],
                    ],
                ],
            ],
        ];
    }
}
