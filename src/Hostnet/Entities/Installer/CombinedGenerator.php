<?php
namespace Hostnet\Entities\Installer;

use Composer\IO\IOInterface;

use Symfony\Component\Finder\SplFileInfo;

use Composer\Package\PackageInterface;

/**
 * The generator for stage 2 that only has to hook into composer
 * It generates the combined entity and repository traits
 * Generated/ClientTrait and Generated/ClientRepositoryTrait
 *
 * @author Nico Schoenmaker <nschoenmaker@hostnet.nl>
 */
class CombinedGenerator
{
  private $io;

  private $environment;

  private $entity_package;

  public function __construct(IOInterface $io, \Twig_Environment $environment, EntityPackage $entity_package)
  {
    $this->io = $io;
    $this->environment = $environment;
    $this->entity_package = $entity_package;
  }

  /**
   * Ask the generator to generate all the trait of traits, and their matching combined interfaces
   * @return void
   */
  public function generate()
  {
    foreach($this->entity_package->getPackageIO()->getEntities() as $file) {
      $class_name = strstr($file->getFilename(), '.', true);
      $traits = $this->recursivelyFindTraitsFor($this->entity_package, $class_name);
      $this->generateTrait($file, $class_name, $traits);
      $this->generateInterface($file, $class_name, $traits);
    }
  }

  private function recursivelyFindTraitsFor(EntityPackage $entity_package, $class_name)
  {
    $result = array();
    foreach($entity_package->getDependentPackages() as $dependent_package) {
      /* @var $package EntityPackage */
      $result = array_merge($result, $this->recursivelyFindTraitsFor($dependent_package, $class_name));
    }
    $file = $entity_package->getPackageIO()->getEntityOrEntityTrait($class_name);
    if($file) {
      $namespace = $this->convertPathToNamespace($file->getRelativePath());
      $result[$namespace] = str_replace('\\', '', $namespace);
    }
    return $result;
  }

  private function generateTrait(SplFileInfo $file, $class_name, array $traits)
  {
    $this->io->write('    Generating trait of traits for <info>' . $class_name. '</info>.');
    $namespace = $this->convertPathToNamespace($file->getRelativePath() . '/Generated');
    $data = $this->environment->render('traits.php.twig', array('class_name' => $class_name, 'namespace' => $namespace, 'use_statements' => $traits));
    $this->entity_package->getPackageIO()->writeGeneratedFile($file->getRelativePath() . '/Generated/', $class_name . 'Traits.php', $data);
  }

  private function generateInterface(SplFileInfo $file, $class_name, array $traits)
  {
    $this->io->write('    Generating combined interface for <info>' . $class_name. '</info>.');
    $namespace = $this->convertPathToNamespace($file->getRelativePath() . '/Generated');
    $data = $this->environment->render('combined_interface.php.twig', array('class_name' => $class_name, 'namespace' => $namespace, 'use_statements' => $traits));
    $this->entity_package->getPackageIO()->writeGeneratedFile($file->getRelativePath() . '/Generated/', $class_name . 'Interface.php', $data);
  }

  /**
   * @todo these lines are also in the installer
   * @param string $path
   * @return string Namespace
   */
  private function convertPathToNamespace($path)
  {
    return str_replace("/", "\\", $path);
  }
}