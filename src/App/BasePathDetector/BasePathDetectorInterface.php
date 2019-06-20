<?php
declare(strict_types=1);


namespace TravelSorter\App\BasePathDetector;


use TravelSorter\App\BasePathDetector\Exception\DocumentRootIsRequired;
use TravelSorter\App\BasePathDetector\Exception\PublicDirectoryPathCanNotBeRelative;
use TravelSorter\App\BasePathDetector\Exception\PublicDirectoryPathIsRequired;

interface BasePathDetectorInterface
{
    /**
     * The "base path" will point to the "public" folder of the application’s root.
     *
     * You will need the base path to prepend the base URL to the URLs (usually inside an `href` attribute) in order for
     * paths to resources to be correct.
     *
     * Usage Example
     * The following assume that the base URL of the page/application is "/mypage".
     *
     * ```php
     * // Prints: <base href="/mypage/" />
     * <base href="<?= $basePathDetector->basePath() ?>" />
     *
     * // Prints: <link rel="stylesheet" type="text/css" href="/mypage/css/base.css" />
     * <link rel="stylesheet" type="text/css" href="/mypage/css/base.css" />
     * ```
     *
     * @throws \TravelSorter\App\BasePathDetector\Exception\DocumentRootIsRequired
     * @throws \TravelSorter\App\BasePathDetector\Exception\PublicDirectoryPathIsRequired
     * @throws PublicDirectoryPathCanNotBeRelative
     */
    public function detect(): string;
}