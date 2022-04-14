<?php
/**
 * @copyright 2014-present Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Component\EntityPlugin;

/**
 * Generates the Abstract*Trait and *TraitInterface
 * As the class name tells you, they are generated with a empty class body
 *
 * Why on earth would we do this?
 * In order to use reflection we have to have valid classes. So we first generate
 * empty interfaces and traits. Secondly we fill the interfaces with their methods.
 */
class EmptyGenerator extends ReflectionGenerator
{
    /**
     * @see \Hostnet\Component\EntityPlugin\ReflectionGenerator::getMethods()
     */
    protected function getMethods(PackageClass $package_class): array
    {
        return [];
    }

    /**
     * {@inheritDoc})
     */
    protected function getParentClass(PackageClass $package_class): ?PackageClass
    {
        return null;
    }
}
