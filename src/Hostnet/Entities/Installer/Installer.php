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
class Installer extends LibraryInstaller
{
  const PACKAGE_TYPE = 'hostnet-entity';

  public function __construct(IOInterface $io, Composer $composer, $type = 'library')
  {
    parent::__construct($io, $composer, $type);
    $composer->getEventDispatcher()->bind(ScriptEvents::PRE_AUTOLOAD_DUMP, array($this, 'postAutoloadDump'));
  }

  public function supports($packageType)
  {
    return self::PACKAGE_TYPE === $packageType;
  }

  public function postAutoloadDump()
  {
    $this->io->write('    <info>Helllo, world!</info>');
  }

  /**
   * This is a bit nasty, but we need to generate the Generated\Client class here.
   * For that we need to know all the combinations, i.e. combine ClientTrait and ClientContractTrait
   * into one class
   */
  public function __destructzzzz()
  {
    $local_repository = $this->composer->getRepositoryManager()->getLocalRepository();
    $hostnet_entities = array();

    /* @var $local_repository \Composer\Repository\RepositoryInterface */
    foreach($local_repository->getPackages() as $package) {
      /* @var $package \Composer\Package\PackageInterface */
      if($this->supports($package->getType())) {
        $hostnet_entities[] = $package;
      }
    }
    $this->io->write(print_r($hostnet_entities, true));
    $this->io->write('<info>Thats all folks!</info>');
  }

  protected function installBinaries(PackageInterface $package)
  {
    parent::installBinaries($package);

    // TODO how to handle autoloading?
    // TODO don't do this in Installer class, but create own class for it
    require_once(__DIR__ . '/../../../../../../twig/twig/lib/Twig/Autoloader.php');
    \Twig_Autoloader::register();

    $this->io->write("  - Generating abstract traits, interfaces and normal class for");

    foreach($this->findTraits($package) as $file) {
      /* @var $file \Symfony\Component\Finder\SplFileInfo */

      // Since this runs before the autoloader is generated, we need to require it ourselves
      require_once($file->getPathname());
      $namespace = str_replace("/", "\\", $file->getRelativePath());
      $trait_name = $file->getBasename('.' . $file->getExtension());
      $this->io->write('    - <info>' . $trait_name . '</info>');

      // Ensure directory exists
      $generated_dir = $file->getPath() . '/Generated';
      if(!is_dir($generated_dir)) {
        if(!mkdir($file->getPath() . '/Generated')) {
          throw new \RuntimeException('Could not create "Generated" directory');
        }
      }

      $class = new \ReflectionClass($namespace . '\\' . $trait_name);

      $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../Resources/templates/');
      $twig = new \Twig_Environment($loader);

      // Replace suffix "Trait" by "Interface"
      $interface_name = strstr($trait_name, 'Trait', true) . 'Interface';
      $generated_namespace = $namespace . '\Generated';
      $params = array('trait_name' => $trait_name, 'name' => $interface_name, 'namespace' => $generated_namespace, 'methods' => $class->getMethods());
      $interface = $twig->render('interface.php.twig', $params);
      file_put_contents($generated_dir . '/' . $interface_name .'.php', $interface);

      $params['name'] = 'Abstract' . $trait_name;
      $abstract_trait = $twig->render('abstract_trait.php.twig', $params);
      file_put_contents($generated_dir . '/' . $params['name'] .'.php', $abstract_trait);
    }

    $this->io->write("");
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
}
