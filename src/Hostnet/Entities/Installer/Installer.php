<?php
namespace Hostnet\Entities\Installer;

use Composer\Package\PackageInterface;

use Composer\Installer\LibraryInstaller;

/**
 * Custom installer to generate the various traits and interfaces for that package
 * @author Nico Schoenmaker <nschoenmaker@hostnet.nl>
 */
class Installer extends LibraryInstaller
{
  public function supports($packageType)
  {
    return 'hostnet-entity' === $packageType;
  }

  protected function installBinaries(PackageInterface $package)
  {
    parent::installBinaries($package);
    $this->io->write("Hello, world");
  }
}
