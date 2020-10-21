<?php
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

    public function getName()
    {
        $name = $this->type->getName();

        // Some types can not be qualified
        if (in_array($name, self::NON_QUALIFIED_TYPES, true)) {
            return $name;
        }

        return '\\' . $name;
    }

    public function allowsNull()
    {
        return $this->type->allowsNull();
    }
}
