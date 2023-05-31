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
        if ($this->type instanceof \ReflectionUnionType) {
            $names = [];
            foreach ($this->type->getTypes() as $type) {
                $names[] = $this->resolveName($type->getName());
            }

            return implode('|', $names);
        }

        return $this->resolveName($this->type->getName());
    }

    public function allowsNull(): bool
    {
        return $this->type->allowsNull();
    }

    private function resolveName(string $name): string
    {
        // Some types can not be qualified
        if (in_array($name, self::NON_QUALIFIED_TYPES, true)) {
            return $name;
        }

        return '\\' . $name;
    }
}
