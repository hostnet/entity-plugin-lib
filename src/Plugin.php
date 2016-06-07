<?php
namespace Hostnet\Component\EntityPlugin;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\ScriptEvents;
use Hostnet\Component\EntityPlugin\Compound\CompoundGenerator;
use Hostnet\Component\EntityPlugin\Compound\PackageContentProvider;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    private $installer;

    public function activate(Composer $composer, IOInterface $io)
    {
        // We don't really have a DI container, so lets create the "services" here.
        $writer           = new Writer();
        $loader           = new \Twig_Loader_Filesystem(__DIR__ . '/Resources/templates/');
        $twig_environment = new \Twig_Environment($loader);

        $compound_generators   = [];
        $compound_generators[] = new CompoundGenerator(
            $io,
            $twig_environment,
            $writer,
            new PackageContentProvider(PackageContent::ENTITY)
        );
        $compound_generators[] = new CompoundGenerator(
            $io,
            $twig_environment,
            $writer,
            new PackageContentProvider(PackageContent::REPOSITORY)
        );

        $empty_generator = new EmptyGenerator($twig_environment, $writer);

        $this->installer = new Installer($io, $composer, $compound_generators, $empty_generator);
    }

    public static function getSubscribedEvents()
    {
        return [
            ScriptEvents::POST_AUTOLOAD_DUMP => ['onPostAutoloadDump', 10 ],
            ScriptEvents::PRE_AUTOLOAD_DUMP => ['onPreAutoloadDump', 10 ],
        ];
    }

    /**
     * Gets called on the POST_AUTOLOAD_DUMP event
     */
    public function onPostAutoloadDump()
    {
        $this->installer->postAutoloadDump();
    }

    /**
     * Gets called on the POST_AUTOLOAD_DUMP event
     */
    public function onPreAutoloadDump()
    {
        $this->installer->preAutoloadDump();
    }
}