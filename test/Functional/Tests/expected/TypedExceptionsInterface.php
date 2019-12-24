<?php
namespace Hostnet\FunctionalFixtures\Entity\Generated;

/**
 * Implement this interface in TypedExceptions!
 * This is a combined interface that will automatically extend to contain the required functions.
 */
interface TypedExceptionsInterface
{

    /**
     * @throws \InvalidArgumentException
     */
    public function throwGlobalException();

    /**
     * @throws \Hostnet\FunctionalFixtures\Exception\DomainException
     */
    public function throwImportedException();

    /**
     * @throws \Hostnet\FunctionalFixtures\Exception\AnotherDomainException
     */
    public function throwNamespacedException();
}
