<?php
namespace Hostnet\FunctionalFixtures\Entity;

use Hostnet\Component\EntityPlugin\ReflectionGenerator;

class TypedParameters
{
    /**
     */
    public function oneParameter(\DateTime $date)
    {
    }

    /**
     */
    public function oneOptionalParameter(\DateTime $date = null)
    {
    }

    /**
     */
    public function anArray(array $foo)
    {
    }

    /**
     */
    public function aNamespacedArgument(ReflectionGenerator $generator)
    {
    }
}
