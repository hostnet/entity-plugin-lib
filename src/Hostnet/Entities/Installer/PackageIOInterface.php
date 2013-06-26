<?php
namespace Hostnet\Entities\Installer;

use Symfony\Component\Finder\SplFileInfo;

/**
 * Interface representing all file reads and writes to a package
 * So we can mock out the file IO during unit-tests
 *
 * @author Nico Schoenmaker <nschoenmaker@hostnet.nl>
 */
interface PackageIOInterface
{

  /**
   * Get the entities contained in the package
   * @return SplFileInfo[]
   */
  public function getEntities();

  /**
   * Get an entity, or entity trait by name, provided it exists in this package
   * @param string $name
   * @return Symfony\Component\Finder\SplFileInfo|null
   */
  public function getEntityOrEntityTrait($name);

  /**
   * Get all entity traits in the package
   * @return SplFileInfo[]
   */
  public function getEntityTraits();

  /**
   * Get the repositories contained in the package
   * @return SplFileInfo[]
   */
  public function getServices();

  /**
   * Get the repository traits contained in the package
   * @return SplFileInfo[]
   */
  public function getServiceTraits();

  /**
   * Get the generated files contained in the package
   * @return SplFileInfo[]
   */
  public function getGeneratedFiles();

  /**
   * Write a generated file to the package.
   * @param $directory The directory relative to the root url for the package
   * @param $file The filename
   * @param $data The data to write
   */
  public function writeGeneratedFile($directory, $file, $data);
}