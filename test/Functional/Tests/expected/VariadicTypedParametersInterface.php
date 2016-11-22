<?php
namespace Hostnet\FunctionalFixtures\Entity\Generated;

/**
 * Implement this interface in VariadicTypedParameters!
 * This is a combined interface that will automatically extend to contain the required functions.
 */
interface VariadicTypedParametersInterface
{

    /**
     * @param \Hostnet\Component\EntityPlugin\ReflectionGenerator[] ...$generator
     */
    public function aNamespacedVariadicArgument(\Hostnet\Component\EntityPlugin\ReflectionGenerator ...$generator);

    /**
     * @param \array[] ...$arrays
     */
    public function variadicByReference(array &...$arrays);
}
