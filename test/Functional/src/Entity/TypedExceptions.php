<?php
namespace Hostnet\FunctionalFixtures\Entity;

use Hostnet\FunctionalFixtures\Exception\AnotherDomainException;
use Hostnet\FunctionalFixtures\Exception\DomainException;

class TypedExceptions
{
    /**
     * @throws \InvalidArgumentException
     */
    public function throwGlobalException()
    {
        throw new \InvalidArgumentException();
    }

    /**
     * @throws DomainException
     */
    public function throwImportedException()
    {
        throw new DomainException();
    }

    /**
     * @throws \Hostnet\FunctionalFixtures\Exception\AnotherDomainException
     */
    public function throwNamespacedException()
    {
        throw new AnotherDomainException();
    }
}
