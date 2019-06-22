<?php
declare(strict_types=1);


namespace TravelSorter\App\Dispatcher;


use TravelSorter\App\BasePathDetector\BasePathDetectorInterface;

class DispatcherAggregate implements DispatcherInterface
{
    /**
     * @var BasePathDetectorInterface
     */
    protected $basePathDetector;
    /**
     * @var DispatcherInterface[]
     */
    protected $dispatchers;

    /**
     * DispatcherAggregate constructor.
     * @param BasePathDetectorInterface $basePathDetector
     * @param DispatcherInterface[] $dispatchers
     */
    public function __construct(BasePathDetectorInterface $basePathDetector, array $dispatchers)
    {
        $this->dispatchers = $dispatchers;
        $this->basePathDetector = $basePathDetector;
    }

    public function dispatch(string $requestRoute, string $httpMethod): ?DispatcherResponse
    {
        $dispatcherResponse = null;
        foreach ($this->dispatchers as $dispatcher) {
            if ($dispatcherResponse = $dispatcher->dispatch($requestRoute, $httpMethod)) {
                return $dispatcherResponse;
            }
        }

        return null;
    }

    /**
     * @param string $basePath
     * @param string $requestUri
     * @return string
     */
    public static function getRequestRoute(string $basePath, string $requestUri): string
    {
        $basePath = rtrim($basePath, '/');

        $requestUri = parse_url($requestUri, PHP_URL_PATH);
        $requestUri = $requestUri === null ? '' : $requestUri;
        $requestUri = rtrim($requestUri, '/');

        $requestRoute = preg_replace(sprintf('~^%s~', preg_quote($basePath, '~')), '', $requestUri);
        $requestRoute = preg_replace('~^(.*?)index.php$~', '$1', $requestRoute);
        $requestRoute = '/' . ltrim($requestRoute, '/');

        return $requestRoute;
    }

    /**
     * @return BasePathDetectorInterface
     */
    public function getBasePathDetector(): BasePathDetectorInterface
    {
        return $this->basePathDetector;
    }

    /**
     * @return DispatcherInterface[]
     */
    public function getDispatchers(): array
    {
        return $this->dispatchers;
    }
}