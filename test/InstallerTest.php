<?php
/**
 * @copyright 2014-present Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Component\EntityPlugin;

use Composer\Composer;
use Composer\Config;
use Composer\EventDispatcher\EventDispatcher;
use Composer\IO\NullIO;
use Composer\Package\RootPackage;
use Composer\Repository\InstalledArrayRepository;
use Composer\Repository\RepositoryManager;
use Composer\Repository\WritableArrayRepository;
use Composer\Util\HttpDownloader;
use Hostnet\Component\EntityPlugin\Mock\Installer as MockInstaller;
use phpunit\framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \Hostnet\Component\EntityPlugin\Installer
 * @covers \Hostnet\Component\EntityPlugin\Installer
 */
class InstallerTest extends TestCase
{
    use ProphecyTrait;

    private $working_dir;

    protected function setUp(): void
    {
        $this->working_dir = __DIR__ . '/..';
    }

    public function testGetInstallPath(): void
    {
        $empty                = $this->prophesize('Hostnet\Component\EntityPlugin\EmptyGenerator')->reveal();
        $reflection_generator = $this->prophesize(ReflectionGenerator::class)->reveal();
        $installer            = new Installer(
            $this->mockIO(),
            $this->mockComposer(),
            [],
            $empty,
            $reflection_generator
        );

        $root_package = new RootPackage('hostnet/root-package', 1, 1);
        $this->assertEquals('.', $installer->getInstallPath($root_package));

        $installer = new MockInstaller($this->mockIO(), $this->mockComposer(), [], $empty, $reflection_generator);

        $package = $this->createMock('Composer\Package\PackageInterface');
        $package->expects($this->once())->method('getPrettyName')->will($this->returnValue('prettyName'));
        $this->assertEquals($this->working_dir . '/vendor/prettyName', $installer->getInstallPath($package));
        $this->assertEquals(1, $installer->initialize_vendor_dir_called);
    }

    public function testGetSourcePath(): void
    {
        $empty                = $this->prophesize('Hostnet\Component\EntityPlugin\EmptyGenerator')->reveal();
        $reflection_generator = $this->prophesize(ReflectionGenerator::class)->reveal();
        $installer            = new Installer(
            $this->mockIO(),
            $this->mockComposer(),
            [],
            $empty,
            $reflection_generator
        );

        $root_package = new RootPackage('hostnet/root-package', 1, 1);
        $this->assertEquals('./src', $installer->getSourcePath($root_package));
        $root_package->setExtra(['entity-bundle-dir' => 'src/Hostnet/FooBundle']);
        $this->assertEquals('./src/Hostnet/FooBundle', $installer->getSourcePath($root_package));
    }

    private function mockComposer()
    {
        $composer = new Composer();
        $composer->setPackage(new RootPackage('hostnet/root-package', 1, 1));
        $composer->setConfig($this->mockConfig());
        $composer->setRepositoryManager($this->mockRepositoryManager());
        $composer->setEventDispatcher(new EventDispatcher($composer, $this->mockIO()));
        return $composer;
    }

    private function mockRepositoryManager()
    {
        $config             = $this->mockConfig();
        $io                 = $this->mockIO();
        $http_downloader    = new HttpDownloader($io, $config);
        $repository_manager = new RepositoryManager($io, $config, $http_downloader);
        $repository_manager->setLocalRepository(new InstalledArrayRepository());
        return $repository_manager;
    }

    private function mockIO()
    {
        return new NullIO();
    }

    private function mockConfig()
    {
        return new Config(true, $this->working_dir);
    }
}
