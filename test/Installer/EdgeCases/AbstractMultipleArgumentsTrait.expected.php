<?php
namespace Hostnet\EdgeCases\Entity\Generated;

/**
 * Trait generated for MultipleArguments
 * Because it's not allowed to combine ClientContractTrait and ClientTaskTrait (that both implement ClientTrait)
 * And a trait is not allowed to implement an interface
 *
 * But having this abstract trait at least ensures auto completion
 * ClientContractTrait should use this trait
 */
trait AbstractMultipleArgumentsTrait
{

    /**
     */
    abstract public function hiGuys($a, $b);
}
