<?php
namespace Hostnet\Component\EntityPlugin;

use Composer\Config;
use Composer\Package\RootPackage;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hostnet\Component\EntityPlugin\Plugin
 */
class PluginTest extends TestCase
{
    public function testActivate()
    {
        $plugin   = new Plugin();
        $prophecy = $this->prophesize('Composer\Composer');
        $prophecy->getPackage()->willReturn(new RootPackage('hostnet/root-package', 1, 1));
        $prophecy->getConfig()->willReturn(new Config());
        $prophecy->getDownloadManager()->willReturn(null);
        $composer = $prophecy->reveal();
        $io       = $this->prophesize('Composer\IO\IOInterface')->reveal();
        self::assertNull($plugin->activate($composer, $io));
    }

    public function testGetSubscribedEvents()
    {
        $this->assertTrue(is_array(Plugin::getSubscribedEvents()));
    }
}
