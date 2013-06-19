<?php
namespace Hostnet\Entities\Installer;

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

  public function __construct(IOInterface $io, Composer $composer, $type = 'library')
  {
    parent::__construct($io, $composer, $type);
    $composer->getEventDispatcher()->bind(ScriptEvents::POST_AUTOLOAD_DUMP, array($this, 'postAutoloadDump'));
  }

  public function supports($packageType)
  {
    return self::PACKAGE_TYPE === $packageType;
  }

  public function getSourcePath(PackageInterface $package)
  {
    return $this->getInstallPath($package) . '/src';
  }

  public function postAutoloadDump()
  {
    $this->io->write('<info>Generating files for entities, pass 1</info>');
    $local_repository = $this->composer->getRepositoryManager()->getLocalRepository();
    $supported_packages = $this->getSupportedPackages($local_repository->getPackages());
    $graph = new EntityPackageBuilder($this, $supported_packages);
    foreach($graph->getEntityPackages() as $entity_package) {
      /* @var $entity_package EntityPackage */
      $this->io->write('  - Now at package <info>' . $entity_package->getPackage()->getName() . '</info>');
      $generator = new CombinedGenerator($this->io, $this->getTwigEnvironment(), $entity_package);
      $generator->generate();
    }
    $this->io->write('<info>Generating files for entities, pass 2</info>');
    foreach($graph->getEntityPackages() as $entity_package) {
      /* @var $entity_package EntityPackage */
      $this->io->write('  - Now at package <info>' . $entity_package->getPackage()->getName() . '</info>');
      foreach($entity_package->getPackageIO()->getEntities() as $entity) {
        $generator = new SingleGenerator($this->io, $this->getTwigEnvironment(), $entity_package->getPackageIO(), $entity);
        $generator->generate();
      }
      foreach($entity_package->getPackageIO()->getEntityTraits() as $entity) {
        $generator = new SingleGenerator($this->io, $this->getTwigEnvironment(), $entity_package->getPackageIO(), $entity);
        $generator->generate();
      }
    }
  }

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
   * Finds all the traits in a package. At least, assuming they are following our coding standard
   * @param PackageInterface $package
   * @return \Symfony\Component\Finder\Finder
   */
  private function findTraits(PackageInterface $package)
  {
    $finder = new Finder();
    $path = $this->getInstallPath($package) . '/src';
    $finder->files()->in($path)->name('*Trait.php');
    return $finder;
  }

  private function getTwigEnvironment()
  {
    if(!$this->twig_environment) {
      require_once(__DIR__ . '/../../../../../../twig/twig/lib/Twig/Autoloader.php');
      \Twig_Autoloader::register();
      $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../Resources/templates/');
      $this->twig_environment = new \Twig_Environment($loader);
    }
    return $this->twig_environment;
  }
}
