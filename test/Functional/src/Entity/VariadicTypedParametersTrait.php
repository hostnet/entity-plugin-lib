<?php
namespace Hostnet\FunctionalFixtures\Entity;

use Hostnet\Component\EntityPlugin\ReflectionGenerator;

trait VariadicTypedParametersTrait
{
    /**
     * @param ReflectionGenerator[] ...$generator
     */
    public function aNamespacedVariadicArgument(ReflectionGenerator ...$generator)
    {
    }

    /**
     * @param \array[] ...$arrays
     */
    public function variadicByReference(array &...$arrays)
    {
    }
}
