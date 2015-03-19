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
     * Get the classes contained in the package matching the type
     *
     * @return PackageClass[]
     */
    public function getClasses();

    /**
     * Get a class, or trait by name, provided it exists in this package
     *
     * @param string $name
     * @return PackageClass|null
     */
    public function getClassOrTrait($name);

    /**
     * Get all matching optional traits for the given entity name in this package
     *
     * @param string $name
     * @return OptionalPackageTrait[]
     */
    public function getOptionalTraits($name);

    /**
     * Get all matching traits inside the package
     *
     * @return PackageClass[]
     */
    public function getTraits();

    /**
     * Returns if the entity exists in the package
     * @param string $short_name the short name of the entity to check for
     * @return bool
     */
    public function hasClass($short_name);
}
