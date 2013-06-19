<?php
namespace Hostnet\Entities\Installer;

use Composer\Package\Package;

use Composer\Script\ScriptEvents;

use Composer\Repository\InstalledRepositoryInterface;

use Composer\IO\IOInterface;

use Composer\Composer;

use Symfony\Component\Finder\Finder;

use Composer\Package\PackageInterface;

use Composer\Installer\LibraryInstaller;

/**
 * Custom installer to generate the various traits and interfaces for that package
 * Assumption: installers are singletons, so this is the only installer for this type
 * @author Nico Schoenmaker <nschoenmaker@hostnet.nl>
 */
class Installer extends LibraryInstaller implements PackagePathResolver
{
  private $twig_environment = false;

  const PACKAGE_TYPE = 'hostnet-entity';

  /**
   * @param IOInterface $io
   * @param Composer $composer
   * @param string $type
   */
  public function __construct(IOInterface $io, Composer $composer, $type = 'library')
  {
    parent::__construct($io, $composer, $type);
    $composer->getEventDispatcher()->bind(ScriptEvents::POST_AUTOLOAD_DUMP, array($this, 'postAutoloadDump'));
  }

  /**
   * @see \Composer\Installer\LibraryInstaller::supports()
   */
  public function supports($packageType)
  {
    return self::PACKAGE_TYPE === $packageType;
  }

  /**
   * @see \Hostnet\Entities\Installer\PackagePathResolver::getSourcePath()
   */
  public function getSourcePath(PackageInterface $package)
  {
    return $this->getInstallPath($package) . '/src';
  }

  /**
   * Gets called on the POST_AUTOLOAD_DUMP event
   */
  public function postAutoloadDump()
  {
    $this->io->write('<info>Generating code for entities</info>');
    $local_repository = $this->composer->getRepositoryManager()->getLocalRepository();
    $supported_packages = $this->getSupportedPackages($local_repository->getPackages());
    $this->setUpAutoloading($supported_packages);
    $graph = new EntityPackageBuilder($this, $supported_packages);

    $this->io->write('<info>Pass 1/3: Generating compound traits and interfaces</info>');
    $this->generateCompoundCode($graph);

    $this->io->write('<info>Pass 2/3: Preparing individual generation</info>');
    $this->generateEmptyCode($graph);

    $this->io->write('<info>Pass 3/3: Performing individual generation</info>');
    $this->generateConcreteIndividualCode($graph);
  }

  /**
   * Gives all packages that we need to install
   * @param array $packages
   * @return \Composer\Package\PackageInterface[]
   */
  private function getSupportedPackages(array $packages)
  {
    $supported_packages = array();
    foreach($packages as $package) {
      /* @var $package \Composer\Package\PackageInterface */
      if($this->supports($package->getType())) {
        $supported_packages[] = $package;
      }
    }
    return $supported_packages;
  }

  /**
   * Ensures all the packages given are autoloaded
   * @param array $supported_packages
   */
  private function setUpAutoloading(array $supported_packages)
  {
    foreach($supported_packages as $package) {
      $generator = $this->composer->getAutoloadGenerator();
      $download_path = $this->getInstallPath($package);
      $map = $generator->parseAutoloads(array(array($package, $download_path)), new Package('dummy', '1.0.0.0', '1.0.0'));
      $class_loader = $generator->createLoader($map);
      $class_loader->register();
    }
  }

  /**
   * Phase 1: Generates compound code
   * @param EntityPackageBuilder $graph
   */
  private function generateCompoundCode(EntityPackageBuilder $graph)
  {
    foreach($graph->getEntityPackages() as $entity_package) {
      /* @var $entity_package EntityPackage */
      $this->io->write('  - Now at package <info>' . $entity_package->getPackage()->getName() . '</info>');
      $generator = new CompoundGenerator($this->io, $this->getTwigEnvironment(), $entity_package);
      $generator->generate();
    }
  }

  /**
   * Phase 2: Ensure all interfaces and traits exist
   * @see EmptyGenerator
   * @param EntityPackageBuilder $graph
   */
  private function generateEmptyCode(EntityPackageBuilder $graph)
  {
    foreach($graph->getEntityPackages() as $entity_package) {
      /* @var $entity_package EntityPackage */
      $this->io->write('  - Now at package <info>' . $entity_package->getPackage()->getName() . '</info>');
      foreach($entity_package->getPackageIO()->getEntities() as $entity) {
        $generator = new EmptyGenerator($this->io, $this->getTwigEnvironment(), $entity_package->getPackageIO(), $entity);
        $generator->generate();
      }
      foreach($entity_package->getPackageIO()->getEntityTraits() as $entity) {
        $generator = new EmptyGenerator($this->io, $this->getTwigEnvironment(), $entity_package->getPackageIO(), $entity);
        $generator->generate();
      }
    }
  }

  /**
   * Phase 3: Ensure all interfaces and traits are filled with correct methods
   * @param EntityPackageBuilder $graph
   */
  private function generateConcreteIndividualCode(EntityPackageBuilder $graph)
  {
    foreach($graph->getEntityPackages() as $entity_package) {
      /* @var $entity_package EntityPackage */
      $this->io->write('  - Now at package <info>' . $entity_package->getPackage()->getName() . '</info>');
      foreach($entity_package->getPackageIO()->getEntities() as $entity) {
        $generator = new ReflectionGenerator($this->io, $this->getTwigEnvironment(), $entity_package->getPackageIO(), $entity);
        $generator->generate();
      }
      foreach($entity_package->getPackageIO()->getEntityTraits() as $entity) {
        $generator = new ReflectionGenerator($this->io, $this->getTwigEnvironment(), $entity_package->getPackageIO(), $entity);
        $generator->generate();
      }
    }
  }

  /**
   * @return \Twig_Environment
   */
  private function getTwigEnvironment()
  {
    if(!$this->twig_environment) {
      // TODO remove this once composer issue #187 is fixed
      // @see https://github.com/composer/composer/issues/187
      require_once(__DIR__ . '/../../../../../../twig/twig/lib/Twig/Autoloader.php');
      \Twig_Autoloader::register();
      $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../Resources/templates/');
      $this->twig_environment = new \Twig_Environment($loader);
    }
    return $this->twig_environment;
  }
}
