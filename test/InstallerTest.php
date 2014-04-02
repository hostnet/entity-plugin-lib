<?php
namespace Hostnet\Component\EntityPlugin;

use Composer\Composer;
use Composer\Config;
use Composer\EventDispatcher\EventDispatcher;
use Composer\IO\NullIO;
use Composer\Package\RootPackage;
use Composer\Repository\RepositoryManager;
use Composer\Repository\WritableArrayRepository;

class InstallerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetInstallPath()
    {
        $installer = new Installer($this->mockIO(), $this->mockComposer());

        $root_package = new RootPackage('hostnet/root-package', 1, 1);
        $this->assertEquals('.', $installer->getInstallPath($root_package));

        $installer = new MockInstallerForInstallerTest($this->mockIO(), $this->mockComposer());

        $package = $this->getMock('Composer\Package\PackageInterface');
        $package->expects($this->once())->method('getPrettyName')->will($this->returnValue('prettyName'));
        $this->assertEquals('vendor/prettyName', $installer->getInstallPath($package));
        $this->assertEquals(1, $installer->initialize_vendor_dir_called);
    }

    public function testGetSourcePath()
    {
        $installer = new Installer($this->mockIO(), $this->mockComposer());

        $root_package = new RootPackage('hostnet/root-package', 1, 1);
        $this->assertEquals('./src', $installer->getSourcePath($root_package));
        $root_package->setExtra(['entity-bundle-dir' => 'src/Hostnet/FooBundle']);
        $this->assertEquals('./src/Hostnet/FooBundle', $installer->getSourcePath($root_package));
    }

    private function mockComposer()
    {
        $composer = new Composer();
        $composer->setConfig($this->mockConfig());
        $composer->setRepositoryManager($this->mockRepositoryManager());
        $composer->setEventDispatcher(new EventDispatcher($composer, $this->mockIO()));
        return $composer;
    }

    private function mockRepositoryManager()
    {
        $repository_manager = new RepositoryManager($this->mockIO(), $this->mockConfig());
        $repository_manager->setLocalRepository(new WritableArrayRepository());
        return $repository_manager;
    }

    private function mockIO()
    {
        return new NullIO();
    }

    private function mockConfig()
    {
        return new Config();
    }
}

class MockInstallerForInstallerTest extends Installer
{
    public $initialize_vendor_dir_called = 0;

    protected function initializeVendorDir()
    {
        $this->initialize_vendor_dir_called++;
    }
}
