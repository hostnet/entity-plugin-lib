<?php
/**
 * @copyright 2014-present Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Component\EntityPlugin;

use Composer\Package\PackageInterface;

/**
 * Represents one "hostnet-entity" package, that knows about
 * - The packages it requires
 * - The packages that require it
 * - The files in that package
 */
class EntityPackage
{
    private $package;

    private $entity_content;

    private $repo_content;

    private $required_packages = [];

    private $dependent_packages = [];

    public function __construct(
        PackageInterface $package,
        PackageContentInterface $entity_content,
        PackageContentInterface $repo_content
    ) {
        $this->package        = $package;
        $this->entity_content = $entity_content;
        $this->repo_content   = $repo_content;
    }

    public function getPackage(): PackageInterface
    {
        return $this->package;
    }

    public function getEntityContent(): PackageContentInterface
    {
        return $this->entity_content;
    }

    public function getRepositoryContent(): PackageContentInterface
    {
        return $this->repo_content;
    }

    /**
     * @return array An array of package links defining required packages
     */
    public function getRequires(): array
    {
        return $this->package->getRequires();
    }

    public function addRequiredPackage(EntityPackage $package): void
    {
        $this->required_packages[] = $package;
    }

    public function getRequiredPackages(): array
    {
        return $this->required_packages;
    }

    public function addDependentPackage(EntityPackage $package): void
    {
        $this->dependent_packages[] = $package;
    }

    public function getDependentPackages(): array
    {
        return $this->dependent_packages;
    }

    public function getSuggests(): array
    {
        return $this->package->getSuggests();
    }

    /**
     * Flattens the dependency tree of this package.
     * All the packages it directly or indirectly references will be uniquely in this list.
     * @return EntityPackage[]
     */
    public function getFlattenedRequiredPackages(): array
    {
        $result = [];
        foreach ($this->getRequiredPackages() as $dependency) {
            $name          = $dependency->getPackage()->getName();
            $result[$name] = $dependency;
            $result        = array_merge($result, $dependency->getFlattenedRequiredPackages());
        }
        return $result;
    }
}
