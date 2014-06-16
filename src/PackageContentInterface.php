<?php
namespace Hostnet\Component\EntityPlugin;

/**
 * Interface representing all file reads and writes to a package
 * So we can mock out the file IO during unit-tests
 *
 * @author Nico Schoenmaker <nschoenmaker@hostnet.nl>
 */
interface PackageContentInterface
{
    /**
     * Get the entities contained in the package
     *
     * @return PackageClass[]
     */
    public function getEntities();

    /**
     * Get an entity, or entity trait by name, provided it exists in this package
     *
     * @param string $name
     * @return PackageClass null
     */
    public function getEntityOrEntityTrait($name);

    /**
     * Get all optional entity traits for the given entity name in this package
     *
     * @param string $name
     * @return OptionalPackageTrait[]
     */
    public function getOptionalEntityTraits($name);

    /**
     * Get all entity traits in the package
     *
     * @return PackageClass[]
     */
    public function getEntityTraits();

    /**
     * Get the repositories contained in the package
     *
     * @return PackageClass[]
     */
    public function getServices();

    /**
     * Get the repository traits contained in the package
     *
     * @return PackageClass[]
     */
    public function getServiceTraits();

    /**
     * Returns if the entity is available in the package
     * @param $shortName the short name of the entity to check for
     * @return bool
     */
    public function hasEntity($shortName);
}
