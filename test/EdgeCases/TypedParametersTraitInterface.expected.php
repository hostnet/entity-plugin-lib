<?php
namespace Hostnet\EdgeCases\Entity\Generated;

/**
 * Interface generated for TypedParameters.
 * This is an internal interface, not to be used in any typehint.
 */
interface TypedParametersTraitInterface
{

    /**
     */
    public function oneParameter(\DateTime $date);
    /**
     */
    public function oneOptionalParameter(\DateTime $date = null);
    /**
     */
    public function anArray(array $foo);
    /**
     */
    public function aNamespacedArgument(\Hostnet\Component\EntityPlugin\ReflectionGenerator $generator);
}
