<?php
/**
 * @copyright 2014-present Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Component\EntityPlugin;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\ScriptEvents;
use Hostnet\Component\EntityPlugin\Compound\CompoundGenerator;
use Hostnet\Component\EntityPlugin\Compound\PackageContentProvider;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    private $installer;

    public function activate(Composer $composer, IOInterface $io): void
    {
        // We don't really have a DI container, so lets create the "services" here.
        $filesystem       = new Filesystem();
        $loader           = new FilesystemLoader(__DIR__ . '/Resources/templates/');
        $twig_environment = new Environment($loader);

        $compound_generators   = [];
        $compound_generators[] = new CompoundGenerator(
            $io,
            $twig_environment,
            $filesystem,
            new PackageContentProvider(PackageContent::ENTITY)
        );
        $compound_generators[] = new CompoundGenerator(
            $io,
            $twig_environment,
            $filesystem,
            new PackageContentProvider(PackageContent::REPOSITORY)
        );

        $empty_generator      = new EmptyGenerator($twig_environment, $filesystem);
        $reflection_generator = new ReflectionGenerator($twig_environment, $filesystem);

        $this->installer = new Installer($io, $composer, $compound_generators, $empty_generator, $reflection_generator);
    }

    public function deactivate(Composer $composer, IOInterface $io): void
    {
    }

    public function uninstall(Composer $composer, IOInterface $io): void
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ScriptEvents::POST_AUTOLOAD_DUMP => ['onPostAutoloadDump', 10 ],
            ScriptEvents::PRE_AUTOLOAD_DUMP  => ['onPreAutoloadDump', 10 ],
        ];
    }

    /**
     * Gets called on the POST_AUTOLOAD_DUMP event
     */
    public function onPostAutoloadDump(): void
    {
        $this->installer->postAutoloadDump();
    }

    /**
     * Gets called on the POST_AUTOLOAD_DUMP event
     */
    public function onPreAutoloadDump(): void
    {
        $this->installer->preAutoloadDump();
    }
}
