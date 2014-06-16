<?php
namespace Hostnet\Component\EntityPlugin;

use Composer\Package\PackageInterface;

/**
 * Represents one "hostnet-entity" package, that knows about
 * - The packages it requires
 * - The packages that require it
 * - The files in that package
 *
 * @author Nico Schoenmaker <nschoenmaker@hostnet.nl>
 */
class EntityPackage
{

    private $package;

    private $package_io;

    private $required_packages = array();

    private $dependent_packages = array();

    public function __construct(PackageInterface $package, PackageContentInterface $package_content)
    {
        $this->package         = $package;
        $this->package_content = $package_content;
    }

    /**
     *
     * @return PackageInterface
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     *
     * @return PackageContentInterface
     */
    public function getPackageContent()
    {
        return $this->package_content;
    }

    /**
     *
     * @return array An array of package links defining required packages
     */
    public function getRequires()
    {
        return $this->package->getRequires();
    }

    public function addRequiredPackage(EntityPackage $package)
    {
        $this->required_packages[] = $package;
    }

    public function getRequiredPackages()
    {
        return $this->required_packages;
    }

    public function addDependentPackage(EntityPackage $package)
    {
        $this->dependent_packages[] = $package;
    }

    public function getDependentPackages()
    {
        return $this->dependent_packages;
    }

    public function getSuggests()
    {
        return $this->package->getSuggests();
    }
}
