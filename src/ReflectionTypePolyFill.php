<?php
/**
 * @copyright 2016-present Hostnet B.V.
 */
declare(strict_types=1);

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

    public function getName(): string
    {
        return $this->name;
    }

    public function allowsNull(): bool
    {
        return $this->allows_null;
    }
}
