<?php
namespace Hostnet\Component\EntityPlugin;

use Composer\Composer;
use Composer\Installer\LibraryInstaller;
use Composer\IO\IOInterface;
use Composer\Package\Package;
use Composer\Package\PackageInterface;
use Composer\Package\RootPackage;
use Composer\Package\RootPackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Script\ScriptEvents;

/**
 * Custom installer to generate the various traits and interfaces for that package
 * Assumption: installers are singletons, so this is the only installer for this type
 *
 * Outputs the phases we go through to the IOInterface
 * If verbose, will output package level detail
 * If very verbose, will output class level detail
 * @todo Cut the dependency to LibraryInstaller, this does not make sense now we are a plugin.
 * @author Nico Schoenmaker <nschoenmaker@hostnet.nl>
 */
class Installer extends LibraryInstaller implements PackagePathResolver
{
    const PACKAGE_TYPE            = 'hostnet-entity';
    const EXTRA_ENTITY_BUNDLE_DIR = 'entity-bundle-dir';

    private $twig_environment = false;

    private $graph = null;

    /**
     * @see \Hostnet\Component\EntityPlugin\PackagePathResolver::getSourcePath()
     */
    public function getSourcePath(PackageInterface $package)
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
    public function getInstallPath(PackageInterface $package)
    {
        if ($package instanceof RootPackageInterface) {
            return '.';
        }
        return parent::getInstallPath($package);
    }

    /**
     * Calculate the dependency graph
     * @return \Hostnet\Component\EntityPlugin\EntityPackageBuilder
     */
    private function getGraph()
    {
        if ($this->graph === null) {
            $local_repository   = $this->composer->getRepositoryManager()->getLocalRepository();
            $packages           = $local_repository->getPackages();
            $packages[]         = $this->composer->getPackage();
            $supported_packages = $this->getSupportedPackages($packages);
            $this->setUpAutoloading($supported_packages);
            $this->graph = new EntityPackageBuilder($this, $supported_packages);
        }
        return $this->graph;
    }

    /**
     * Gets called on the PRE_AUTOLOAD_DUMP event
     */
    public function preAutoloadDump()
    {
        $graph = $this->getGraph();
        $this->io->write('<info>Pass 1/3: Generating compound traits and interfaces</info>');
        $this->generateCompoundCode($graph);

        $this->io->write('<info>Pass 2/3: Preparing individual generation</info>');
        $this->generateEmptyCode($graph);
    }

    /**
     * Gets called on the POST_AUTOLOAD_DUMP event
     */
    public function postAutoloadDump()
    {
        $graph = $this->getGraph();

        $this->io->write('<info>Pass 3/3: Performing individual generation</info>');
        $this->generateConcreteIndividualCode($graph);
    }
    /**
     * Gives all packages that we need to install
     *
     * @param array $packages
     * @return \Composer\Package\PackageInterface[]
     */
    private function getSupportedPackages(array $packages)
    {
        $supported_packages = array();
        foreach ($packages as $package) {
            /* @var $package \Composer\Package\PackageInterface */
            if ($this->supportsPackage($package)) {
                $supported_packages[] = $package;
            }
        }
        return $supported_packages;
    }

    /**
     * @param PackageInterface $package
     * @return boolean
     */
    private function supportsPackage(PackageInterface $package)
    {
        $extra = $package->getExtra();
        if (self::PACKAGE_TYPE === $package->getType() || isset($extra[self::EXTRA_ENTITY_BUNDLE_DIR])) {
            return true;
        }
    }

    /**
     * Ensures all the packages given are autoloaded
     *
     * @param array $supported_packages
     */
    private function setUpAutoloading(array $supported_packages)
    {
        foreach ($supported_packages as $package) {
            $generator     = $this->composer->getAutoloadGenerator();
            $download_path = $this->getInstallPath($package);
            $map           = $generator->parseAutoloads(
                array(
                    array(
                        $package,
                        $download_path
                    )
                ),
                new Package('dummy', '1.0.0.0', '1.0.0')
            );
            $class_loader  = $generator->createLoader($map);
            $class_loader->register();
        }
    }

    /**
     * Phase 1: Generates compound code
     *
     * @param EntityPackageBuilder $graph
     */
    private function generateCompoundCode(EntityPackageBuilder $graph)
    {
        foreach ($graph->getEntityPackages() as $entity_package) {
            /* @var $entity_package EntityPackage */
            $this->writeIfVerbose(
                '    - Generating for package <info>'.
                $entity_package->getPackage()->getName() . '</info>'
            );

            $generator = new CompoundGenerator(
                $this->io,
                $this->getTwigEnvironment(),
                $entity_package,
                new Writer()
            );

            $generator->generate();
        }
    }

    /**
     * Phase 2: Ensure all interfaces and traits exist
     *
     * @see EmptyGenerator
     * @param EntityPackageBuilder $graph
     */
    private function generateEmptyCode(EntityPackageBuilder $graph)
    {
        foreach ($graph->getEntityPackages() as $entity_package) {
            /* @var $entity_package EntityPackage */
            $this->writeIfVerbose(
                '    - Preparing package <info>' . $entity_package->getPackage()
                    ->getName() . '</info>'
            );
            foreach ($entity_package->getPackageIO()->getEntities() as $entity) {
                $this->writeIfVeryVerbose(
                    '        - Preparing interface and abstract trait for <info>' . $entity->getName() . '</info>'
                );
                $generator = new EmptyGenerator(
                    $this->io,
                    $this->getTwigEnvironment(),
                    new Writer(),
                    $entity
                );
                $generator->generate();
            }
            foreach ($entity_package->getPackageIO()->getEntityTraits() as $entity) {
                $this->writeIfVeryVerbose(
                    '        - Preparing interface and abstract trait for <info>' . $entity->getName() . '</info>'
                );
                $generator = new EmptyGenerator(
                    $this->io,
                    $this->getTwigEnvironment(),
                    new Writer(),
                    $entity
                );
                $generator->generate();
            }
        }
    }

    /**
     * Phase 3: Ensure all interfaces and traits are filled with correct methods
     *
     * @param EntityPackageBuilder $graph
     */
    private function generateConcreteIndividualCode(EntityPackageBuilder $graph)
    {
        foreach ($graph->getEntityPackages() as $entity_package) {
            /* @var $entity_package EntityPackage */
            $this->writeIfVerbose(
                '    - Generating for package <info>' . $entity_package->getPackage()
                    ->getName() . '</info>'
            );
            foreach ($entity_package->getPackageIO()->getEntities() as $entity) {
                $this->writeIfVeryVerbose(
                    '        - Generating interface and abstract trait for <info>' . $entity->getName() . '</info>'
                );
                ReflectionGenerator::generateInIsolation($entity->getName(), $entity->getGeneratedDirectory());
            }
            foreach ($entity_package->getPackageIO()->getEntityTraits() as $entity) {
                $this->writeIfVeryVerbose(
                    '        - Generating interface and abstract trait for <info>' . $entity->getName() . '</info>'
                );
                ReflectionGenerator::generateInIsolation($entity->getName(), $entity->getGeneratedDirectory());
            }
        }
    }

    private function writeIfVerbose($text)
    {
        if ($this->io->isVerbose()) {
            $this->io->write($text);
        }
    }

    private function writeIfVeryVerbose($text)
    {
        if ($this->io->isVeryVerbose()) {
            $this->io->write($text);
        }
    }

    /**
     *
     * @return \Twig_Environment
     */
    private function getTwigEnvironment()
    {
        if (! $this->twig_environment) {
            $loader                 = new \Twig_Loader_Filesystem(__DIR__ . '/Resources/templates/');
            $this->twig_environment = new \Twig_Environment($loader);
        }
        return $this->twig_environment;
    }
}
