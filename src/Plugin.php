<?php
namespace Hostnet\Component\EntityPlugin;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\ScriptEvents;

class Plugin implements PluginInterface, EventSubscriberInterface
{

    private $installer;

    public function activate(Composer $composer, IOInterface $io)
    {
        $this->installer = new Installer($io, $composer);
    }

    public static function getSubscribedEvents()
    {
        return array(
            ScriptEvents::POST_AUTOLOAD_DUMP => array(
                'onPostAutoloadDump',
                0
            )
        );
    }

    /**
     * Gets called on the POST_AUTOLOAD_DUMP event
     */
    public function onPostAutoloadDump()
    {
        $this->installer->postAutoloadDump();
    }
}
