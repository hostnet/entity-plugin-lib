<?php
namespace Hostnet\Entities\Installer;

use Composer\Package\PackageInterface;

/**
 * A thing able to resolve the install dir for a PackageInterface
 *
 * @author Nico Schoenmaker <nschoenmaker@hostnet.nl>
 */
interface PackagePathResolver
{
  /**
   * Get the path containing the actual source of the application
   * @param PackageInterface $package
   */
  public function getSourcePath(PackageInterface $package);
}