<?php
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

    /**
     * @return string
     */
    public function getName()
    {
        return $this->parameter->getName();
    }

    /**
     * @return ReflectionTypeInterface|null
     */
    public function getType()
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

    /**
     * @return bool
     */
    public function hasType()
    {
        if (method_exists($this->parameter, 'getType')) {
            return $this->parameter->hasType();
        }

        return null !== $this->getType();
    }

    /**
     * @return bool
     */
    public function allowsNull()
    {
        return $this->parameter->allowsNull();
    }

    /**
     * @return bool
     */
    public function isOptional()
    {
        return $this->parameter->isOptional();
    }

    /**
     * @return bool
     */
    public function isDefaultValueAvailable()
    {
        return $this->parameter->isDefaultValueAvailable();
    }

    /**
     * @return bool
     */
    public function isVariadic()
    {
        return $this->parameter->isVariadic();
    }

    /**
     * @return bool
     */
    public function isPassedByReference()
    {
        return $this->parameter->isPassedByReference();
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->parameter->getDefaultValue();
    }
}
