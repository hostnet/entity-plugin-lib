<?php
namespace Hostnet\Component\EntityPlugin;

class ReflectionTypePolyFill implements ReflectionTypeInterface
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $allows_null;

    public function __construct($name, $allows_null)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('First parameter should be a string.');
        }
        if (!is_bool($allows_null)) {
            throw new \InvalidArgumentException('Second parameter should be a boolean.');
        }

        $this->name        = $name;
        $this->allows_null = (true === $allows_null);
    }

    public function getName()
    {
        return $this->name;
    }

    public function allowsNull()
    {
        return $this->allows_null;
    }
}
