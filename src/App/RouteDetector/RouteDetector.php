<?php
declare(strict_types=1);


namespace TravelSorter\App\RouteDetector;


use Psr\Http\Message\UriInterface;
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

    public function detect(UriInterface $uri): string
    {
        $basePath = rtrim($this->basePathDetector->detect(), '/');

        $uriPath = $uri->getPath();
        $uriPath = $uriPath === null ? '' : $uriPath;
        $uriPath = rtrim($uriPath, '/');

        $requestRoute = preg_replace(sprintf('~^%s~', preg_quote($basePath, '~')), '', $uriPath);
        $requestRoute = preg_replace('~^(.*?)index.php$~', '$1', $requestRoute);
        $requestRoute = '/' . ltrim($requestRoute, '/');

        return $requestRoute;
    }
}