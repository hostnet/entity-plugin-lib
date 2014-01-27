<?php
namespace Hostnet\EdgeCases\Entity\Generated;

/**
 * Trait generated for TypedParameters
 * Because it's not allowed to combine ClientContractTrait and ClientTaskTrait (that both implement ClientTrait)
 * And a trait is not allowed to implement an interface
 *
 * But having this abstract trait at least ensures auto completion
 * ClientContractTrait should use this trait
 */
trait AbstractTypedParametersTrait
{

    /**
     */
    abstract public function oneParameter(\DateTime $date);
    /**
     */
    abstract public function oneOptionalParameter(\DateTime $date = null);
    /**
     */
    abstract public function anArray(array $foo);
    /**
     */
    abstract public function aNamespacedArgument(\Hostnet\Component\EntityPlugin\ReflectionGenerator $generator);
}
