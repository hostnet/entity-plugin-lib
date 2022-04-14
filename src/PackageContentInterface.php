<?php
/**
 * @copyright 2014-present Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Component\EntityPlugin;

/**
 * Interface representing all file reads and writes to a package
 * So we can mock out the file IO during unit-tests
 */
interface PackageContentInterface
{
    /**
     * Get the classes contained in the package matching the type
     *
     * @return PackageClass[]
     */
    public function getClasses(): array;

    /**
     * Get a class, or trait by name, provided it exists in this package
     *
     * @param string $name
     */
    public function getClassOrTrait($name): ?PackageClass;

    /**
     * Get all matching optional traits for the given entity name in this package
     *
     * @param string $name
     * @return OptionalPackageTrait[]
     */
    public function getOptionalTraits($name): array;

    /**
     * Get all matching traits inside the package
     *
     * @return PackageClass[]
     */
    public function getTraits(): array;

    /**
     * Returns if the entity exists in the package
     * @param string $short_name the short name of the entity to check for
     */
    public function hasClass($short_name): bool;
}
