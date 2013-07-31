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
class CompoundGenerator
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
      $traits = $this->recursivelyFindUseStatementsFor($this->entity_package, $class_name);
      $relative_path = $file->getRelativePath();
      $this->generateTrait($relative_path, $class_name, $traits);
      $this->generateInterface($relative_path, $class_name, $traits);
    }
  }

  /**
   * Gives all the entities to be required in the compound interface
   * Also generates a unique alias for them
   *
   * @param EntityPackage $entity_package
   * @param string $class_name
   * @return array[]UseStatement
   */
  private function recursivelyFindUseStatementsFor(EntityPackage $entity_package, $class_name)
  {
    $result = array();
    foreach($entity_package->getDependentPackages() as $dependent_package) {
      /* @var $package EntityPackage */
      $result = array_merge($result, $this->recursivelyFindUseStatementsFor($dependent_package, $class_name));
    }
    $file = $entity_package->getPackageIO()->getEntityOrEntityTrait($class_name);
    if($file) {
      $namespace = $this->convertPathToNamespace($file->getRelativePath());
      $result[] = new UseStatement($namespace, $file);
    }
    return $result;
  }

  /**
   * Generates Generated/<class_name>Traits.php
   * @param string $relative_path The relative path to the directory to generate the trait in
   * @param string $class_name
   * @param array $traits
   */
  private function generateTrait($relative_path, $class_name, array $traits)
  {
    $this->writeIfVeryVerbose('    - Generating trait of traits for <info>' . $class_name. '</info>');
    $namespace = $this->convertPathToNamespace($relative_path);
    $data = $this->environment->render('traits.php.twig', array('class_name' => $class_name, 'namespace' => $namespace, 'use_statements' => $traits));
    $this->entity_package->getPackageIO()->writeGeneratedFile($relative_path, $class_name . 'Traits.php', $data);
  }

  /**
   * Generates Generated/<class_name>Interfaces.php
   * @param string $relative_path The relative path to the directory to generate the interface in
   * @param string $class_name
   * @param array $traits
   */
  private function generateInterface($relative_path, $class_name, array $traits)
  {
    $this->writeIfVeryVerbose('    - Generating combined interface for <info>' . $class_name. '</info>');
    $namespace = $this->convertPathToNamespace($relative_path);
    $data = $this->environment->render('combined_interface.php.twig', array('class_name' => $class_name, 'namespace' => $namespace, 'use_statements' => $traits));
    $this->entity_package->getPackageIO()->writeGeneratedFile($relative_path, $class_name . 'Interface.php', $data);
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

  private function writeIfVeryVerbose($text)
  {
    if($this->io->isVeryVerbose()) {
      $this->io->write($text);
    }
  }
}