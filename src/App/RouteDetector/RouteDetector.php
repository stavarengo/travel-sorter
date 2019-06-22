<?php
declare(strict_types=1);


namespace TravelSorter\App\RouteDetector;


use TravelSorter\App\BasePathDetector\BasePathDetectorInterface;

class RouteDetector implements RouteDetectorInterface
{
    /**
     * @var BasePathDetectorInterface
     */
    private $basePathDetector;

    /**
     * RouteDetector constructor.
     * @param BasePathDetectorInterface $basePathDetector
     */
    public function __construct(BasePathDetectorInterface $basePathDetector)
    {
        $this->basePathDetector = $basePathDetector;
    }

    public function detect(string $requestUri): string
    {
        $basePath = rtrim($this->basePathDetector->detect(), '/');

        $requestUri = parse_url($requestUri, PHP_URL_PATH);
        $requestUri = $requestUri === null ? '' : $requestUri;
        $requestUri = rtrim($requestUri, '/');

        $requestRoute = preg_replace(sprintf('~^%s~', preg_quote($basePath, '~')), '', $requestUri);
        $requestRoute = preg_replace('~^(.*?)index.php$~', '$1', $requestRoute);
        $requestRoute = '/' . ltrim($requestRoute, '/');

        return $requestRoute;
    }
}