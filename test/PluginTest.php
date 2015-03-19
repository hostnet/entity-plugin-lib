<?php
namespace Hostnet\Component\EntityPlugin;

use Composer\Config;

/**
 * @covers Hostnet\Component\EntityPlugin\Plugin
 */
class PluginTest extends \PHPUnit_Framework_TestCase
{
    public function testActivate()
    {
        $plugin = new Plugin();
        $prophecy = $this->prophesize('Composer\Composer');
        $prophecy->getConfig()->willReturn(new Config());
        $prophecy->getDownloadManager()->willReturn(null);
        $composer = $prophecy->reveal();
        $io = $this->prophesize('Composer\IO\IOInterface')->reveal();
        $plugin->activate($composer, $io);
    }

    public function testGetSubscribedEvents()
    {
        $this->assertTrue(is_array(Plugin::getSubscribedEvents()));
    }
}
