<?php
namespace Hostnet\Component\EntityPlugin\Functional;

use Composer\Autoload\AutoloadGenerator;
use Composer\Composer;
use Composer\Config;
use Composer\EventDispatcher\EventDispatcher;
use Composer\IO\BufferIO;
use Composer\Package\RootPackage;
use Composer\Repository\InstalledArrayRepository;
use Composer\Repository\RepositoryManager;
use Hostnet\Component\EntityPlugin\Plugin;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Console\Output\StreamOutput;

class GenerationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Plugin
     */
    private $plugin;
    private $composer;
    private $io;

    private $last_wdir;

    protected function setUp()
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

        $repo_manager       = new RepositoryManager($this->io, $config);
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

    public function testGeneration()
    {
        $this->plugin->activate($this->composer, $this->io);
        $this->plugin->onPreAutoloadDump();
        $this->plugin->onPostAutoloadDump();

        // test the output
        $dir = __DIR__ . '/../src/Entity/Generated';
        self::assertEquals(
            file_get_contents(__DIR__ . '/expected/BaseClassInterface.php'),
            file_get_contents($dir . '/BaseClassInterface.php')
        );
        self::assertEquals(
            file_get_contents(__DIR__ . '/expected/ExtendedClassInterface.php'),
            file_get_contents($dir . '/ExtendedClassInterface.php')
        );
        self::assertEquals(
            file_get_contents(__DIR__ . '/expected/ConstructShouldNotBePresentInterface.php'),
            file_get_contents($dir . '/ConstructShouldNotBePresentInterface.php')
        );
        self::assertEquals(
            file_get_contents(__DIR__ . '/expected/MultipleArgumentsInterface.php'),
            file_get_contents($dir . '/MultipleArgumentsInterface.php')
        );
        self::assertEquals(
            file_get_contents(__DIR__ . '/expected/TypedParametersInterface.php'),
            file_get_contents($dir . '/TypedParametersInterface.php')
        );
    }

    protected function tearDown()
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
