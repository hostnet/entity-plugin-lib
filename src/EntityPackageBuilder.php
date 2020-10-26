<?php
/**
 * @copyright 2014-present Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Component\EntityPlugin;

use Composer\Autoload\ClassMapGenerator;
use Composer\Package\PackageInterface;
use Composer\Semver\Constraint\Constraint;

/**
 * Builds EntityPackage-s
 * They are nodes in a (dependency) graph
 */
class EntityPackageBuilder
{
    private $resolver;

    private $tree_nodes = [];

    public function __construct(PackagePathResolverInterface $resolver, array $packages)
    {
        $this->resolver = $resolver;
        // First create a hash map for quicker lookup, with fancier objects to store the graph
        foreach ($packages as $package) {
            $this->addPackage($package);
        }
        foreach ($this->tree_nodes as $entity_package) {
            /** @var EntityPackage $entity_package */
            $links = array_merge($entity_package->getRequires(), array_map(function ($str) use ($entity_package) {
                return new \Composer\Package\Link(
                    $entity_package->getPackage()->getName(),
                    $str,
                    new Constraint('=', '1')
                );
            }, array_keys($entity_package->getSuggests())));

            foreach ($links as $link) {
                if ($link instanceof \Composer\Package\Link) {
                    //The target of a $link is it's dependency
                    if (! isset($this->tree_nodes[$link->getTarget()])) {
                        continue;
                    }
                    $entity_package->addRequiredPackage($this->tree_nodes[$link->getTarget()]);
                    $this->tree_nodes[$link->getTarget()]->addDependentPackage($entity_package);
                }
            }
        }
    }

    private function addPackage(PackageInterface $package): void
    {
        $class_map      = ClassMapGenerator::createMap($this->resolver->getSourcePath($package));
        $entity_content = new PackageContent($class_map, PackageContent::ENTITY);
        $repo_content   = new PackageContent($class_map, PackageContent::REPOSITORY);

        $this->tree_nodes[$package->getName()] = new EntityPackage(
            $package,
            $entity_content,
            $repo_content
        );
    }

    /**
     * Get all the converted packages
     *
     * @return EntityPackage[]
     */
    public function getEntityPackages(): array
    {
        return $this->tree_nodes;
    }
}
