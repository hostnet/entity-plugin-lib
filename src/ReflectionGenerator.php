<?php

namespace Hostnet\Component\EntityPlugin;

use Composer\IO\IOInterface;
use Symfony\Component\Finder\SplFileInfo;

/**
 * A simple, light-weight generator that can be used runtime during development
 * It does not know about the composer structure, since thats expensive to build
 * @author Nico Schoenmaker <nschoenmaker@hostnet.nl>
 */
class ReflectionGenerator
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
    $namespace = str_replace("/", "\\", $this->file->getRelativePath());
    $trait_or_class_name = $this->file->getBasename('.' . $this->file->getExtension());

    $interface_name = $this->getInterfaceName($trait_or_class_name);
    $generated_namespace = $namespace . '\Generated';
    $params = array(
                    'trait_or_class_name' => $trait_or_class_name,
                    'name' => $interface_name,
                    'namespace' => $generated_namespace,
                    'type_hinter' => new TypeHinter(),
                    'methods' => $this->getMethods($namespace, $trait_or_class_name)
    );
    $interface = $this->environment->render('trait_interface.php.twig', $params);
    $path = $this->file->getRelativePath();
    $this->package_io->writeGeneratedFile($path, $interface_name . '.php', $interface);

    $params['name'] = $this->getAbstractTraitName($trait_or_class_name);
    $abstract_trait = $this->environment->render('abstract_trait.php.twig', $params);
    $this->package_io->writeGeneratedFile($path, $params['name'] . '.php', $abstract_trait);
  }

  /**
   * Which methods do we have to generate?
   * @param string $namespace
   * @param string $trait_or_class_name
   * @return \ReflectionMethod[]
   */
  protected function getMethods($namespace, $trait_or_class_name)
  {
    // TODO remove this once composer issue #187 is fixed
    // @see https://github.com/composer/composer/issues/187
    //require_once ($this->file->getPathname());
    $class = new \ReflectionClass($namespace . '\\' . $trait_or_class_name);
    $methods = $class->getMethods();
    foreach($methods as $key => $method) {
      if($method->name === '__construct') {
        unset($methods[$key]);
      }
    }
    return $methods;
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
