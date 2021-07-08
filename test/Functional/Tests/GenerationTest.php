<?php
/**
 * @copyright 2016-present Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Component\EntityPlugin\Functional;

use Composer\Autoload\AutoloadGenerator;
use Composer\Composer;
use Composer\Config;
use Composer\EventDispatcher\EventDispatcher;
use Composer\Installer\InstallationManager;
use Composer\IO\BufferIO;
use Composer\Package\RootPackage;
use Composer\Repository\InstalledArrayRepository;
use Composer\Repository\RepositoryManager;
use Composer\Util\HttpDownloader;
use Composer\Util\Loop;
use Hostnet\Component\EntityPlugin\Installer;
use Hostnet\Component\EntityPlugin\Plugin;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * @coversNothing
 */
class GenerationTest extends TestCase
{
    /**
     * @var Plugin
     */
    private $plugin;
    private $composer;
    private $io;

    private $last_wdir;

    protected function setUp(): void
    {
        $this->last_wdir = getcwd();

        chdir(__DIR__ . '/../');

        $this->plugin   = new Plugin();
        $this->composer = new Composer();
        $this->io       = new BufferIO('', StreamOutput::VERBOSITY_VERY_VERBOSE);
        $config         = new Config(false);
        $config->merge(
            ['config' => ['vendor-dir' => __DIR__]]
        );
        $http_downloader = new HttpDownloader($this->io, $config);
        $loop            = new Loop($http_downloader);
        $this->composer->setInstallationManager(new InstallationManager($loop, $this->io));
        $repo_manager       = new RepositoryManager($this->io, $config, $http_downloader);
        $repository         = new InstalledArrayRepository();
        $package            = new RootPackage('foobar', 1, 1);
        $event_dispatcher   = new EventDispatcher($this->composer, $this->io);
        $autoload_generator = new AutoloadGenerator($event_dispatcher, $this->io);

        $package->setType('hostnet-entity');
        $repo_manager->setLocalRepository($repository);

        $this->composer->setConfig($config);
        $this->composer->setRepositoryManager($repo_manager);
        $this->composer->setPackage($package);
        $this->composer->setAutoloadGenerator($autoload_generator);
    }

    public function testGeneration(): void
    {
        $this->plugin->activate($this->composer, $this->io);
        $this->plugin->onPreAutoloadDump();
        $this->plugin->onPostAutoloadDump();

        // test the output
        $dir = __DIR__ . '/../src/Entity/Generated';
        self::assertFileEquals(
            __DIR__ . '/expected/BaseClassInterface.php',
            $dir . '/BaseClassInterface.php'
        );
        self::assertFileEquals(
            __DIR__ . '/expected/ExtendedClassInterface.php',
            $dir . '/ExtendedClassInterface.php'
        );
        self::assertFileEquals(
            __DIR__ . '/expected/ConstructShouldNotBePresentInterface.php',
            $dir . '/ConstructShouldNotBePresentInterface.php'
        );
        self::assertFileEquals(
            __DIR__ . '/expected/MultipleArgumentsInterface.php',
            $dir . '/MultipleArgumentsInterface.php'
        );
        self::assertFileEquals(
            __DIR__ . '/expected/TypedParametersInterface.php',
            $dir . '/TypedParametersInterface.php'
        );
        self::assertFileEquals(
            __DIR__ . '/expected/VariadicTypedParametersInterface.php',
            $dir . '/VariadicTypedParametersInterface.php'
        );
        self::assertFileEquals(
            __DIR__ . '/expected/ExtendedMissingParentClassInterface.php',
            $dir . '/ExtendedMissingParentClassInterface.php'
        );
        self::assertFileEquals(
            __DIR__ . '/expected/DefaultParametersInterface.php',
            $dir . '/DefaultParametersInterface.php'
        );
        self::assertFileEquals(
            __DIR__ . '/expected/WithTraitClassInterface.php',
            $dir . '/WithTraitClassInterface.php'
        );
        self::assertFileEquals(
            __DIR__ . '/expected/TypedExceptionsInterface.php',
            $dir . '/TypedExceptionsInterface.php'
        );
        self::assertFileEquals(
            __DIR__ . '/expected/SpecialCharactersInDocsInterface.php',
            $dir . '/SpecialCharactersInDocsInterface.php'
        );
    }

    public function testGenerationWithoutInterfaces(): void
    {
        $this->composer->getPackage()->setExtra([Installer::GENERATE_INTERFACES => false]);
        $this->plugin->activate($this->composer, $this->io);
        $this->plugin->onPreAutoloadDump();
        $this->plugin->onPostAutoloadDump();

        $dir = __DIR__ . '/../src/Entity/Generated';
        self::assertDirectoryExists($dir);
        foreach (scandir($dir) as $file_name) {
            self::assertStringNotContainsString('Interface', $file_name);
        }
    }

    protected function tearDown(): void
    {
        chdir($this->last_wdir);

        // remove generate stuff in entity directory.
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(__DIR__ . '/../src/Entity/Generated', RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            if ($fileinfo->isDir()) {
                rmdir($fileinfo->getRealPath());
            } else {
                unlink($fileinfo->getRealPath());
            }
        }

        rmdir(__DIR__ . '/../src/Entity/Generated');
    }
}
