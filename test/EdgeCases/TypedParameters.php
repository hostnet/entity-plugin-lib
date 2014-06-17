<?php
namespace Hostnet\EdgeCases\Entity;

use Hostnet\Component\EntityPlugin\ReflectionGenerator;

trait extra
{
    /**
     * I am from a trait
     */
    public function extra()
    {
    }
}

class TypedParameters
{
    use extra;

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
