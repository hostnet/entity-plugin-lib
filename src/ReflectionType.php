<?php
/**
 * @copyright 2016-present Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Component\EntityPlugin;

class ReflectionType implements ReflectionTypeInterface
{
    /**
     * @var \ReflectionType
     */
    private $type;

    public function __construct(\ReflectionType $type)
    {
        $this->type = $type;
    }

    public function getName(): string
    {
        $name = $this->type->getName();

        // Some types can not be qualified
        if (in_array($name, self::NON_QUALIFIED_TYPES, true)) {
            return $name;
        }

        return '\\' . $name;
    }

    public function allowsNull(): bool
    {
        return $this->type->allowsNull();
    }
}
