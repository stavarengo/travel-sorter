<?php
declare(strict_types=1);


namespace TravelSorter\App\BasePathDetector;


use TravelSorter\App\BasePathDetector\Exception\DocumentRootIsRequired;
use TravelSorter\App\BasePathDetector\Exception\PublicDirectoryPathCanNotBeRelative;
use TravelSorter\App\BasePathDetector\Exception\PublicDirectoryPathIsRequired;

class BasePathDetector implements BasePathDetectorInterface
{
    /**
     * This usually will be the `$_SERVER['DOCUMENT_ROOT']`.
     *
     * The root directory of the site defined by the 'DocumentRoot' directive in the General Section
     * or a section e.g. `DOCUMENT_ROOT=/var/www/example`.
     *
     * @var string
     */
    private $documentRoot;
    /**
     * Absolute path to the public directory.
     * Does not use relative path as it can result in unexpected behaviors.
     *
     * @var string
     */
    private $publicDirectoryPath;

    /**
     * BasePathDetector constructor.
     * @param string $documentRoot
     * @param string $publicDirectoryPath
     */
    public function __construct(string $documentRoot, string $publicDirectoryPath)
    {
        $this->documentRoot = $documentRoot;
        $this->publicDirectoryPath = $publicDirectoryPath;
    }

    public function detect(): string
    {
        if (!$this->documentRoot) {
            throw new DocumentRootIsRequired('The document root is required and can not be empty.');
        }

        if (!$this->publicDirectoryPath) {
            throw new PublicDirectoryPathIsRequired('The public directory path is required and can not be empty.');
        }

        $patternToMatchRelativePath = sprintf('~%1$s\.{1,2}%1$s~', preg_quote(DIRECTORY_SEPARATOR, '~'));
        if ($this->publicDirectoryPath[0] !== DIRECTORY_SEPARATOR
            || preg_match($patternToMatchRelativePath, $this->publicDirectoryPath)
        ) {
            throw new PublicDirectoryPathCanNotBeRelative(
                'The public directory can not be relative path. Please provide the absolute path to the public directory.'
            );
        }

        $documentRoot = rtrim($this->documentRoot, DIRECTORY_SEPARATOR);
        $basePath = trim(str_replace($documentRoot, '', $this->publicDirectoryPath), DIRECTORY_SEPARATOR);
        $basePath = preg_replace('~^(.*?)/index.php$~', '$1', $basePath);
        $basePath = trim($basePath, DIRECTORY_SEPARATOR);

        return '/' . $basePath;
    }
}