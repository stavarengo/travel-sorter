<?php
declare(strict_types=1);


namespace TravelSorter\App\RouteDetector;


use TravelSorter\App\BasePathDetector\BasePathDetectorInterface;

interface RouteDetectorInterface
{
    /**
     * Detected which route this $requestUri is direct to.
     *
     * To detect a route, usually you will need to:
     *  - Remove all the protocol and the host of the request URI;
     *  - Remove all the base path;
     *  - Remove any query parameter and components parts from the URL (eg, everything after the question mark character);
     *
     * For example, for a request $requestUri like "http://localhost/public/index.php/api/sort?type=1#anchor"
     *  - Remove the protocol and host, would result in: "/public/index.php/api/sort?type=1#anchor"
     *  - Now remove the base path, the result is: "/api/sort?type=1#anchor"
     *  - Remove everything after the question mark: "/api/sort"
     *
     * Done! In this example, the route is "/api/sort".
     *
     * @return string
     *
     * @see BasePathDetectorInterface
     */
    public function detect(string $requestUri): string;
}