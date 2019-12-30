<?php
namespace Hostnet\FunctionalFixtures\Entity\Generated;

/**
 * Implement this interface in TypedParameters!
 * This is a combined interface that will automatically extend to contain the required functions.
 */
interface TypedParametersInterface
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
