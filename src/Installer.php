<?php
/**
 * @copyright 2014-present Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Component\EntityPlugin;

use Composer\Composer;
use Composer\Installer\LibraryInstaller;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Package\RootPackageInterface;

/**
 * Custom installer to generate the various traits and interfaces for that package
 * Assumption: installers are singletons, so this is the only installer for this type
 *
 * Outputs the phases we go through to the IOInterface
 * If verbose, will output package level detail
 * If very verbose, will output class level detail
 * @todo Cut the dependency to LibraryInstaller, this does not make sense now we are a plugin.
 */
class Installer extends LibraryInstaller implements PackagePathResolverInterface
{
    private const PACKAGE_TYPE            = 'hostnet-entity';
    private const EXTRA_ENTITY_BUNDLE_DIR = 'entity-bundle-dir';
    public const GENERATE_INTERFACES      = 'generate-interfaces';

    private $compound_generators;

    private $empty_generator;

    private $twig_environment = null;

    private $graph = null;

    private $generate_interfaces = true;

    /**
     * @var ReflectionGenerator
     */
    private $reflection_generator;

    public function __construct(
        IOInterface $io,
        Composer $composer,
        array $compound_generators,
        EmptyGenerator $empty_generator,
        ReflectionGenerator $reflection_generator
    ) {
        parent::__construct($io, $composer);
        $this->compound_generators  = $compound_generators;
        $this->empty_generator      = $empty_generator;
        $this->reflection_generator = $reflection_generator;

        $extra = $composer->getPackage()->getExtra();
        if (isset($extra[self::GENERATE_INTERFACES])) {
            $this->generate_interfaces = filter_var($extra[self::GENERATE_INTERFACES], FILTER_VALIDATE_BOOLEAN);
        }
    }

    /**
     * @see \Hostnet\Component\EntityPlugin\PackagePathResolverInterface::getSourcePath()
     */
    public function getSourcePath(PackageInterface $package): string
    {
        $path  = $this->getInstallPath($package);
        $extra = $package->getExtra();
        if (isset($extra[self::EXTRA_ENTITY_BUNDLE_DIR])) {
            return $path . '/' . $extra[self::EXTRA_ENTITY_BUNDLE_DIR];
        }
        return $path . '/src';
    }

    /**
     * Overridden to take into account the root package
     *
     * @see \Composer\Installer\LibraryInstaller::getInstallPath()
     */
    public function getInstallPath(PackageInterface $package): string
    {
        if ($package instanceof RootPackageInterface) {
            return '.';
        }
        return parent::getInstallPath($package);
    }

    /**
     * Calculate the dependency graph
     */
    private function getGraph(): \Hostnet\Component\EntityPlugin\EntityPackageBuilder
    {
        if ($this->graph === null) {
            $local_repository   = $this->composer->getRepositoryManager()->getLocalRepository();
            $packages           = $local_repository->getPackages();
            $packages[]         = $this->composer->getPackage();
            $supported_packages = $this->getSupportedPackages($packages);
            $this->setUpAutoloading();
            $this->graph = new EntityPackageBuilder($this, $supported_packages);
        }
        return $this->graph;
    }

    /**
     * Gets called on the PRE_AUTOLOAD_DUMP event
     */
    public function preAutoloadDump(): void
    {
        $passes = $this->generate_interfaces ? 3 : 1;
        $graph  = $this->getGraph();
        $this->io->write('<info>Pass 1/' . $passes . ': Generating compound traits and interfaces</info>');
        $this->generateCompoundCode($graph);

        if (!$this->generate_interfaces) {
            return;
        }

        $this->io->write('<info>Pass 2/3: Preparing individual generation</info>');
        $this->generateEmptyCode($graph);
    }

    /**
     * Gets called on the POST_AUTOLOAD_DUMP event
     */
    public function postAutoloadDump(): void
    {
        if (!$this->generate_interfaces) {
            return;
        }

        $graph = $this->getGraph();

        $this->io->write('<info>Pass 3/3: Performing individual generation</info>');
        $this->generateConcreteIndividualCode($graph);
    }
    /**
     * Gives all packages that we need to install
     *
     * @param RootPackageInterface[] $packages
     * @return \Composer\Package\PackageInterface[]
     */
    private function getSupportedPackages(array $packages): array
    {
        $supported_packages = [];
        foreach ($packages as $package) {
            /** @var \Composer\Package\PackageInterface $package */
            if ($this->supportsPackage($package)) {
                $supported_packages[] = $package;
            }
        }
        return $supported_packages;
    }

    /**
     * @param PackageInterface $package
     */
    private function supportsPackage(PackageInterface $package): bool
    {
        $extra = $package->getExtra();
        if (self::PACKAGE_TYPE === $package->getType() || isset($extra[self::EXTRA_ENTITY_BUNDLE_DIR])) {
            return true;
        }
        return false;
    }

    /**
     * Ensures all the packages are autoloaded, needed because classes are read using reflection.
     */
    private function setUpAutoloading(): void
    {
        //Pre-required variable's
        $package              = $this->composer->getPackage();
        $autoload_generator   = $this->composer->getAutoloadGenerator();
        $local_repository     = $this->composer->getRepositoryManager()->getLocalRepository();
        $installation_manager = $this->composer->getInstallationManager();

        //API stolen from Composer see DumpAutoloadCommand.php
        $package_map = $autoload_generator->buildPackageMap(
            $installation_manager,
            $package,
            $local_repository->getCanonicalPackages()
        );
        $autoloads   = $autoload_generator->parseAutoloads($package_map, $package);

        //Create the classloader and register the classes.
        $class_loader = $autoload_generator->createLoader($autoloads);
        $class_loader->register();
    }

    /**
     * Phase 1: Generates compound code
     *
     * @param EntityPackageBuilder $graph
     */
    private function generateCompoundCode(EntityPackageBuilder $graph): void
    {
        foreach ($graph->getEntityPackages() as $entity_package) {
            /** @var EntityPackage $entity_package */
            $this->writeIfVerbose(
                '    - Generating for package <info>' .
                $entity_package->getPackage()->getName() . '</info>'
            );
            foreach ($this->compound_generators as $compound_generator) {
                /** @var Compound\CompoundGenerator $compound_generator */
                $compound_generator->generate($entity_package);
            }
        }
    }

    /**
     * Ensure all interfaces and traits exist
     *
     * @see EmptyGenerator
     * @param EntityPackageBuilder $graph
     */
    private function generateEmptyCode(EntityPackageBuilder $graph): void
    {
        foreach ($graph->getEntityPackages() as $entity_package) {
            /** @var EntityPackage $entity_package */
            $this->writeIfVerbose(
                '    - Preparing package <info>' . $entity_package->getPackage()
                    ->getName() . '</info>'
            );
            foreach ($entity_package->getEntityContent()->getClasses() as $entity) {
                $this->writeIfVeryVerbose(
                    '        - Generating empty interface for <info>' . $entity->getName() . '</info>'
                );
                $this->empty_generator->generate($entity);
            }
        }
    }

    /**
     * Ensure all interfaces and traits are filled with correct methods
     *
     * @param EntityPackageBuilder $graph
     */
    private function generateConcreteIndividualCode(EntityPackageBuilder $graph): void
    {
        foreach ($graph->getEntityPackages() as $entity_package) {
            /** @var EntityPackage $entity_package */
            $this->writeIfVerbose(
                '    - Generating for package <info>' . $entity_package->getPackage()
                    ->getName() . '</info>'
            );
            foreach ($entity_package->getEntityContent()->getClasses() as $entity) {
                $this->writeIfVeryVerbose(
                    '        - Generating interface for <info>' . $entity->getName() . '</info>'
                );
                $this->reflection_generator->generate($entity);
            }
        }
    }

    private function writeIfVerbose($text): void
    {
        if ($this->io->isVerbose()) {
            $this->io->write($text);
        }
    }

    private function writeIfVeryVerbose($text): void
    {
        if ($this->io->isVeryVerbose()) {
            $this->io->write($text);
        }
    }
}
