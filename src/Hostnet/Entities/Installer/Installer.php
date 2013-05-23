<?php
namespace Hostnet\Entities\Installer;

use Symfony\Component\Finder\Finder;

use Composer\Package\PackageInterface;

use Composer\Installer\LibraryInstaller;

/**
 * Custom installer to generate the various traits and interfaces for that package
 * @author Nico Schoenmaker <nschoenmaker@hostnet.nl>
 */
class Installer extends LibraryInstaller
{
  public function supports($packageType)
  {
    return 'hostnet-entity' === $packageType;
  }

  protected function installBinaries(PackageInterface $package)
  {
    parent::installBinaries($package);

    // TODO how to handle autoloading?
    // TODO don't do this in Installer class, but create own class for it
    require_once(__DIR__ . '/../../../../../../twig/twig/lib/Twig/Autoloader.php');
    \Twig_Autoloader::register();

    $this->io->write("  - Generating abstract traits, interfaces and normal class for");
    $finder = new Finder();
    $path = $this->getInstallPath($package) . '/src';
    $finder->files()->in($path)->name('*Trait.php');
    foreach($finder as $file) {
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
}
