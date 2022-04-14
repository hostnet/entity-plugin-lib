<?php
namespace Hostnet\Component\EntityPlugin;

use Composer\Config;
use Composer\Downloader\DownloaderInterface;
use Composer\Downloader\DownloadManager;
use Composer\Package\RootPackage;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \Hostnet\Component\EntityPlugin\Plugin
 */
class PluginTest extends TestCase
{
    use ProphecyTrait;

    public function testActivate()
    {
        $plugin   = new Plugin();
        $prophecy = $this->prophesize('Composer\Composer');
        $prophecy->getPackage()->willReturn(new RootPackage('hostnet/root-package', 1, 1));
        $prophecy->getConfig()->willReturn(new Config());
        $prophecy->getDownloadManager()->willReturn($this->prophesize(DownloadManager::class)->reveal());
        $composer = $prophecy->reveal();
        $io       = $this->prophesize('Composer\IO\IOInterface')->reveal();

        self::assertNull($plugin->activate($composer, $io));
    }

    public function testGetSubscribedEvents()
    {
        $this->assertTrue(is_array(Plugin::getSubscribedEvents()));
    }
}
