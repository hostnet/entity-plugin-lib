<?php
namespace Hostnet\Entities\Installer;

use Symfony\Component\Finder\SplFileInfo;

/**
 * Represents a use statement
 * @author Nico Schoenmaker <nschoenmaker@hostnet.nl>
 */
class UseStatement
{
  private $namespace;

  private $file;

  /**
   * @param string $namespace
   * @param SplFileInfo $file
   */
  public function __construct($namespace, SplFileInfo $file)
  {
    $this->namespace = $namespace;
    $this->file = $file;
  }

  /**
   * @return boolean
   */
  public function isTrait()
  {
    return strpos($this->file->getFilename(), 'Trait.php') !== false;
  }

  /**
   * @return string
   */
  public function getNamespace()
  {
    return $this->namespace;
  }

  /**
   * @return string
   */
  public function getAlias()
  {
    return str_replace('\\', '', $this->namespace);
  }
}