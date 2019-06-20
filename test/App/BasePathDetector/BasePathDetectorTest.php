<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace TravelSorter\Test\App\BasePathDetector;

use PHPUnit\Framework\TestCase;
use TravelSorter\App\BasePathDetector\BasePathDetector;
use TravelSorter\App\BasePathDetector\Exception\DocumentRootIsRequired;
use TravelSorter\App\BasePathDetector\Exception\PublicDirectoryPathCanNotBeRelative;
use TravelSorter\App\BasePathDetector\Exception\PublicDirectoryPathIsRequired;

class BasePathDetectorTest extends TestCase
{
    public function testDocumentRootIsEmpty()
    {
        try {
            $basePathDetector = new BasePathDetector('', '/var/www/html/public');
            $basePathDetector->detect();
            $this->fail(
                sprintf(
                    'It did not throw the exception "%s" when the document root was a empty string.',
                    DocumentRootIsRequired::class
                )
            );
        } catch (DocumentRootIsRequired $e) {
            $this->assertEquals(
                'The document root is required and can not be empty.',
                $e->getMessage()
            );
        }
    }

    public function testPublicPathIsEmpty()
    {
        $basePathDetector = new BasePathDetector('/var/www/html', '');

        try {
            $basePathDetector->detect();
            $this->fail(
                sprintf(
                    'It did not throw the exception "%s" when the public directory path was a empty string.',
                    PublicDirectoryPathIsRequired::class
                )
            );
        } catch (PublicDirectoryPathIsRequired $e) {
            $this->assertEquals(
                'The public directory path is required and can not be empty.',
                $e->getMessage()
            );
        }
    }

    /**
     * @dataProvider relativePublicPathProvider
     */
    public function testPublicPathIsRelative(string $relativePublicDirectoryPath)
    {
        $basePathDetector = new \TravelSorter\App\BasePathDetector\BasePathDetector('/var/www/html',
            $relativePublicDirectoryPath);

        try {
            $basePathDetector->detect();
            $this->fail(
                sprintf(
                    'It did not throw the exception "%s" when the public directory path were "%s".',
                    PublicDirectoryPathCanNotBeRelative::class,
                    $relativePublicDirectoryPath
                )
            );
        } catch (PublicDirectoryPathCanNotBeRelative $e) {
            $this->assertEquals(
                'The public directory can not be relative path. Please provide the absolute path to the public directory.',
                $e->getMessage()
            );
        }
    }

    /**
     * @dataProvider basePathProvider
     */
    public function testGetBasePathSuccessfully(
        string $realPublicDirectoryPath,
        string $documentRoot,
        string $expectedBasePath
    ) {
        $this->assertEquals(
            $expectedBasePath,
            (new BasePathDetector($documentRoot, $realPublicDirectoryPath))->detect(),
            sprintf('Failed when document root was "%s".', $documentRoot)
        );

        $documentRootWithSlashAtTheEnd = "$documentRoot/";
        $this->assertEquals(
            $expectedBasePath,
            (new BasePathDetector($documentRootWithSlashAtTheEnd, $realPublicDirectoryPath))->detect(),
            sprintf('Failed when this document root "%s" had a slash in the end.', $documentRoot)
        );

        $publicDirectoryWithSlashAtThePath = "$realPublicDirectoryPath/";
        $this->assertEquals(
            $expectedBasePath,
            (new BasePathDetector($documentRoot, $publicDirectoryWithSlashAtThePath))->detect(),
            sprintf('Failed when the public directory "%s" had a slash in the end.', $realPublicDirectoryPath)
        );

        $this->assertEquals(
            $expectedBasePath,
            (new BasePathDetector($documentRootWithSlashAtTheEnd, $publicDirectoryWithSlashAtThePath))->detect(),
            sprintf(
                'Failed when both public directory ("%s") and document root ("%s") had a slash in the end.',
                $realPublicDirectoryPath,
                $documentRoot
            )
        );
    }

    public function testBasePathShouldNotEndWithSlash()
    {
        $basePathDetector = new BasePathDetector('/var/www/html/', '/var/www/html/public/');

        $this->assertEquals('/public', $basePathDetector->detect());
    }


    public function testTheIndexDotPhpFileMustBeStripOutTheBasePath()
    {
        $this->assertEquals(
            '/public',
            (new BasePathDetector('/var/www/html', '/var/www/html/public/index.php'))->detect()
        );
        $this->assertEquals(
            '/public/another-file.php',
            (new BasePathDetector('/var/www/html', '/var/www/html/public/another-file.php'))->detect()
        );
    }

    public function relativePublicPathProvider(): array
    {
        return [
            ['var/www/html/public'],
            ['/var/www/html/../public'],
            ['/var/www/html/./public'],
            ['./public'],
            ['../public'],
            ['/./public'],
            ['/../public'],
            ['public'],
            ['.'],
            ['..'],
        ];
    }

    public function basePathProvider(): array
    {
        $publicDirectoryPath = '/var/www/html/public';
        return [
            // REAL PUBLIC DIRECTORY PATH, DOCUMENT ROOT, EXPECTED VALUE
            [$publicDirectoryPath, '/var/www/html/public', '/'],
            [$publicDirectoryPath, '/var/www/html', '/public'],
            [$publicDirectoryPath, '/var/www', '/html/public'],
            [$publicDirectoryPath, '/var', '/www/html/public'],
            [$publicDirectoryPath, '/', '/var/www/html/public'],
        ];
    }
}
