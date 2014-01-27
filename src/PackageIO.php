<?php
namespace Hostnet\Component\EntityPlugin;

use Composer\Autoload\ClassMapGenerator;

/**
 * Concrete implementation of the PackageIOInterface
 *
 * @author Nico Schoenmaker <nschoenmaker@hostnet.nl>
 */
class PackageIO implements PackageIOInterface
{
  private $path;

  private $entities = array();

  private $entity_traits = array();

  private $services = array();

  private $service_traits = array();

  private $generated_files = array();

  /**
   * @todo don't do the work in the constructor, but only when asked.
   * @param String $path
   * @param ClassMapperInterface $class_mapper To map the classes
   */
  public function __construct($path, ClassMapperInterface $class_mapper)
  {
    $this->path = $path;
    $class_map = $class_mapper->createClassMap($path);
    foreach($class_map as $class => $file) {
      $package_class = new PackageClass($class, $file);
      // TODO strpos and then use only the last part as values of generated_files. Maybe even keys?
      if(strstr($class, '\\Generated\\')) {
        $this->generated_files[] = $package_class;
      } else if(strpos($class, '\\Entity\\')) {
        $this->addEntity($package_class);
      } else if(strpos($class, '\\Service\\')) {
        $this->addService($package_class);
      }
    }
  }

  private function addEntity(PackageClass $class)
  {
    if ($class->isTrait()) {
        $this->entity_traits[] = $class;
    } else {
        $this->entities[] = $class;
    }
  }

  private function addService(PackageClass $class)
  {
      if ($class->isTrait()) {
          $this->service_traits[] = $class;
      } else {
          $this->services[] = $class;
      }
  }

  /**
   * @see \Hostnet\Component\EntityPlugin\PackageIOInterface::getEntities()
   */
  public function getEntities()
  {
    return $this->entities;
  }

  /**
   * @see \Hostnet\Component\EntityPlugin\PackageIOInterface::getEntityOrEntityTrait()
   */
  public function getEntityOrEntityTrait($name)
  {
    foreach ($this->entities as $class) {
      /* @var $class PackageClass */
      if ($class->getShortName() == $name) {
        return $class;
      }
    }
    $looking_for = $name .'Trait.php';
    foreach ($this->entity_traits as $class) {
        /* @var $class PackageClass */
        if ($class->getShortName() == $name) {
            return $class;
        }
    }
  }

  public function getEntityTraits()
  {
    return $this->entity_traits;
  }

  /**
   * @see \Hostnet\Component\EntityPlugin\PackageIOInterface::getServices()
   */
  public function getServices()
  {
    return $this->services;
  }

  /**
   * @see \Hostnet\Component\EntityPlugin\PackageIOInterface::getServiceTraits()
   */
  public function getServiceTraits()
  {
    return $this->service_traits;
  }

  /**
   * @see \Hostnet\Component\EntityPlugin\PackageIOInterface::getGeneratedFiles()
   */
  public function getGeneratedFiles()
  {
    return $this->generated_files;
  }

  /**
   * @see \Hostnet\Component\EntityPlugin\PackageIOInterface::writeGeneratedFile()
   */
  public function writeGeneratedFile($path, $file, $data)
  {
    $this->ensureDirectoryExists($path);

    if(!is_dir($path)) {
      mkdir($path, 0755, true);
    }
    file_put_contents($path . '/' . $file, $data);
  }

  /**
   * Ensures that the Generated/ folder exists
   * @throws \RuntimeException
   */
  private function ensureDirectoryExists($path)
  {
    if(! is_dir($path)) {
      if(! mkdir($path)) {
        throw new \RuntimeException('Could not create "Generated" directory "' . $path . '"');
      }
    }
  }

}