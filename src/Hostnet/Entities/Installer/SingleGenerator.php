<?php

namespace Hostnet\Entities\Installer;

use Composer\IO\IOInterface;
use Symfony\Component\Finder\SplFileInfo;

/**
 * A simple, light-weight generator that can be used runtime during development
 * It does not know about the composer structure, since thats expensive to build
 * @author Nico Schoenmaker <nschoenmaker@hostnet.nl>
 */
class SingleGenerator
{
  private $io;
  private $environment;
  private $package_io;
  private $file;

  /**
   *
   * @todo We only need the "writeGeneratedFile" function of PackageIO, we
   *       should split the interface
   * @param IOInterface $io
   * @param \Twig_Environment $environment
   * @param PackageIOInterface $package_io
   * @param SplFileInfo $file
   */
  public function __construct(IOInterface $io,\Twig_Environment $environment, PackageIOInterface $package_io, SplFileInfo $file)
  {
    $this->io = $io;
    $this->environment = $environment;
    $this->package_io = $package_io;
    $this->file = $file;
  }

  /**
   * Generates the interface and the abstract trait
   * @throws \RuntimeException
   */
  public function generate()
  {
    // TODO how to handle autoloading?

    // Since this runs before the autoloader is generated, we need to require it
    // ourselves
    require_once ($this->file->getPathname());
    $namespace = str_replace("/", "\\", $this->file->getRelativePath());
    $trait_or_class_name = $this->file->getBasename('.' . $this->file->getExtension());
    $this->io->write('    Generating interface and abstract trait for <info>' . $trait_or_class_name . '</info>');

    // Ensure directory exists
    $generated_dir = $this->file->getPath() . '/Generated';
    if(! is_dir($generated_dir)) {
      if(! mkdir($this->file->getPath() . '/Generated')) {
        throw new \RuntimeException('Could not create "Generated" directory');
      }
    }

    $class = new \ReflectionClass($namespace . '\\' . $trait_or_class_name);
    $interface_name = $this->getInterfaceName($trait_or_class_name);
    $generated_namespace = $namespace . '\Generated';
    $params = array(
                    'trait_or_class_name' => $trait_or_class_name,
                    'name' => $interface_name,
                    'namespace' => $generated_namespace,
                    'methods' => $class->getMethods()
    );
    $interface = $this->environment->render('trait_interface.php.twig', $params);
    file_put_contents($generated_dir . '/' . $interface_name . '.php', $interface);

    $params['name'] = $this->getAbstractTraitName($trait_or_class_name);
    $abstract_trait = $this->environment->render('abstract_trait.php.twig', $params);
    file_put_contents($generated_dir . '/' . $params['name'] . '.php', $abstract_trait);
  }

  private function getInterfaceName($trait_or_class_name)
  {
    if(strpos($trait_or_class_name, 'Trait') !== false) {
      return $trait_or_class_name . 'Interface';
    } else {
      return $trait_or_class_name . 'TraitInterface';
    }
  }

  private function getAbstractTraitName($trait_or_class_name)
  {
    if(strpos($trait_or_class_name, 'Trait') !== false) {
      return 'Abstract' . $trait_or_class_name;
    } else {
      return 'Abstract' . $trait_or_class_name . 'Trait';
    }
  }

}
