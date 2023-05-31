<?php
/**
 * @copyright 2016-present Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Component\EntityPlugin;

/**
 * Add getType and hasType to ReflectionParameter.
 */
class ReflectionParameter
{
    /**
     * @var \ReflectionParameter
     */
    private $parameter;

    /**
     * @param \ReflectionParameter $parameter
     */
    public function __construct(\ReflectionParameter $parameter)
    {
        $this->parameter = $parameter;
    }

    public function getName(): string
    {
        return $this->parameter->getName();
    }

    public function getType(): ?ReflectionTypeInterface
    {
        if (PHP_MAJOR_VERSION >= 7) {
            if ($this->hasType()) {
                return new ReflectionType($this->parameter->getType());
            } else {
                return null;
            }
        }

        $type = null;

        preg_match('/\[\s<\w+?>\s([\\\\\w]+)/', $this->parameter->__toString(), $matches);
        if (isset($matches[1])) {
            if (in_array($matches[1], ReflectionTypeInterface::NON_QUALIFIED_TYPES, true)) {
                $name = $matches[1];
            } else {
                $name = '\\' . $matches[1];
            }
            $type = new ReflectionTypePolyFill($name, $this->parameter->allowsNull());
        }

        return $type;
    }

    public function hasType(): bool
    {
        if (method_exists($this->parameter, 'getType')) {
            return $this->parameter->hasType();
        }

        return null !== $this->getType();
    }

    public function allowsNull(): bool
    {
        return $this->parameter->allowsNull();
    }

    public function isOptional(): bool
    {
        return $this->parameter->isOptional();
    }

    public function isDefaultValueAvailable(): bool
    {
        return $this->parameter->isDefaultValueAvailable();
    }

    public function isVariadic(): bool
    {
        return $this->parameter->isVariadic();
    }

    public function isPassedByReference(): bool
    {
        return $this->parameter->isPassedByReference();
    }

    public function getDefaultValue(): mixed
    {
        return $this->parameter->getDefaultValue();
    }

    public function isDefaultValueConstant(): bool
    {
        return $this->parameter->isDefaultValueConstant();
    }

    public function getDefaultValueConstantName(): ?string
    {
        return $this->parameter->getDefaultValueConstantName();
    }

    /**
     * Returns the default value in a manner which is safe to use in PHP code
     * as a default value.
     *
     * @throws \ReflectionException If called on property without default value
     */
    public function getPhpSafeDefaultValue(): string
    {
        $value     = $this->getDefaultValue();
        $is_string = $this->hasType() && $this->getType()->getName() === 'string';

        if ($value === true) {
            return 'true';
        } elseif ($value === false) {
            return 'false';
        } elseif ($this->isDefaultValueConstant()) {
            if (strpos($this->getDefaultValueConstantName(), 'self') === 0) {
                return $this->getDefaultValueConstantName();
            }
            return '\\' . $this->getDefaultValueConstantName();
        } elseif (is_array($value)) {
            return '[]';
        } elseif (null === $value || ($is_string && $value === 'null' && $this->allowsNull())) {
            return 'null';
        } elseif (is_numeric($value) && !$is_string) {
            return (string) $value;
        }
        return var_export($value, true);
    }
}
