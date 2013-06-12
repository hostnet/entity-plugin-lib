<?php
namespace Hostnet\Entities\Installer;

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

  private $repositories = array();

  private $repository_traits = array();

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
      if(!strpos($namespace, '\\Entities\\')) {
        continue;
      }
      $basename = $file->getBasename();

      // TODO strpos and then use only the last part as values of generated_files. Maybe even keys?
      if(strstr($namespace, '\\Generated')) {
        $this->generated_files[] = $file;
      } else if(strpos($basename, 'RepositoryTrait.php')) {
        $this->repository_traits[] = $file;
      } else if(strpos($basename, 'Trait.php')) {
        $this->entity_traits[] = $file;
      } else if(strpos($basename, 'Repository.php')) {
        $this->repositories[] = $file;
      } else if(strpos($basename, '.php')) {
        $this->entities[] = $file;
      }
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

  /**
   * @see \Hostnet\Entities\Installer\PackageIOInterface::getRepositories()
   */
  public function getRepositories()
  {
    return $this->repositories;
  }

  /**
   * @see \Hostnet\Entities\Installer\PackageIOInterface::getRepositoryTraits()
   */
  public function getRepositoryTraits()
  {
    return $this->repository_traits;
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