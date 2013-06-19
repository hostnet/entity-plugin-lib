<?php
namespace Hostnet\Entities\Installer;

use Symfony\Component\Finder\SplFileInfo;

use Symfony\Component\Finder\Finder;

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
   * @param Finder $finder Used to find all files in repo
   * @param String $path
   */
  public function __construct(Finder $finder, $path)
  {
    $this->path = $path;
    $files = $finder->files()->in($path)->name('*.php');
    foreach($files as $file) {
      /* @var $file \Symfony\Component\Finder\SplFileInfo */
      $namespace = str_replace("/", "\\", $file->getRelativePath());
      // TODO strpos and then use only the last part as values of generated_files. Maybe even keys?
      if(strstr($namespace, '\\Generated')) {
        $this->generated_files[] = $file;
      } else if(strpos($namespace, '\\Entity')) {
        $this->addEntity($file);
      } else if(strpos($namespace, '\\Service')) {
        $this->addService($file);
      }
    }
  }

  private function addEntity(SplFileInfo $file)
  {
    $basename = $file->getBasename();
    if(strpos($basename, 'Trait.php')) {
      $this->entity_traits[] = $file;
    } else if(strpos($basename, '.php')) {
        $this->entities[] = $file;
    }
  }

  private function addService(SplFileInfo $file)
  {
      $basename = $file->getBasename();
      if(strpos($basename, 'ServiceTrait.php')) {
          $this->service_traits[] = $file;
      } else if(strpos($basename, 'Service.php')) {
          $this->services[] = $file;
      }
  }

  /**
   * @see \Hostnet\Entities\Installer\PackageIOInterface::getEntities()
   */
  public function getEntities()
  {
    return $this->entities;
  }

  /**
   * @see \Hostnet\Entities\Installer\PackageIOInterface::getEntityTrait()
   */
  public function getEntityTrait($name)
  {
    $looking_for = $name .'Trait.php';
    foreach($this->entity_traits as $file) {
      /* @var $file \Symfony\Component\Finder\SplFileInfo */
      if($file->getBasename() == $looking_for) {
        return $file;
      }
    }
  }

  public function getEntityTraits()
  {
    return $this->entity_traits;
  }

  /**
   * @see \Hostnet\Entities\Installer\PackageIOInterface::getServices()
   */
  public function getServices()
  {
    return $this->services;
  }

  /**
   * @see \Hostnet\Entities\Installer\PackageIOInterface::getServiceTraits()
   */
  public function getServiceTraits()
  {
    return $this->service_traits;
  }

  /**
   * @see \Hostnet\Entities\Installer\PackageIOInterface::getGeneratedFiles()
   */
  public function getGeneratedFiles()
  {
    return $this->generated_files;
  }

  /**
   * @see \Hostnet\Entities\Installer\PackageIOInterface::writeGeneratedFile()
   */
  public function writeGeneratedFile($directory, $file, $data)
  {
    $path = $this->path . '/' . $directory;
    if(!is_dir($path)) {
      mkdir($path, 0755, true);
    }
    file_put_contents($path . '/' . $file, $data);
  }

}