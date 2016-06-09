<?php
namespace Hostnet\Component\EntityPlugin;

/**
 * Generates the Abstract*Trait and *TraitInterface
 * As the class name tells you, they are generated with a empty class body
 *
 * Why on earth would we do this?
 * In order to use reflection we have to have valid classes. So we first generate
 * empty interfaces and traits. Secondly we fill the interfaces with their methods.
 *
 * @author Hidde Boomsma <hboomsma@hostnet.nl>
 * @author Nico Schoenmaker <nschoenmaker@hostnet.nl>
 */
class EmptyGenerator extends ReflectionGenerator
{

    /**
     *
     * @see \Hostnet\Component\EntityPlugin\ReflectionGenerator::getMethods()
     */
    protected function getMethods(PackageClass $package_class)
    {
        return [];
    }

    /**
     * {@inheritDoc})
     */
    protected function getParentClass(PackageClass $package_class)
    {
        return null;
    }
}
