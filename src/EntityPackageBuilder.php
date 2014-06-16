<?php
namespace Hostnet\Component\EntityPlugin;

use Composer\Package\PackageInterface;

/**
 * Builds EntityPackage-s
 * They are nodes in a (dependency) graph
 *
 * @author Nico Schoenmaker <nschoenmaker@hostnet.nl>
 */
class EntityPackageBuilder
{

    private $resolver;

    private $tree_nodes = array();

    public function __construct(PackagePathResolver $resolver, array $packages)
    {
        $this->resolver = $resolver;
        // First create a hash map for quicker lookup, with fancier objects to store the graph
        foreach ($packages as $package) {
            $this->addPackage($package);
        }
        foreach ($this->tree_nodes as $entity_package) {

            /* @var $entity_package EntityPackage */
            $links = array_merge($entity_package->getRequires(), $entity_package->getSuggests());

            foreach ($links as $link) {
                /* @var $link \Composer\Package\Link */
                // The target of a $link is it's dependency
                if (! isset($this->tree_nodes[$link->getTarget()])) {
                    continue;
                }
                $entity_package->addRequiredPackage($this->tree_nodes[$link->getTarget()]);
                $this->tree_nodes[$link->getTarget()]->addDependentPackage($entity_package);
            }
        }
    }

    private function addPackage(PackageInterface $package)
    {
        $class_map       = (new ClassMapper())->createClassMap($this->resolver->getSourcePath($package));
        $package_content = new PackageContent($class_map);

        $this->tree_nodes[$package->getName()] = new EntityPackage($package, $package_content);
    }

    /**
     * Get all the converted packages
     *
     * @return EntityPackage[]
     */
    public function getEntityPackages()
    {
        return $this->tree_nodes;
    }
}
