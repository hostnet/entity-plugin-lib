<?php
namespace Hostnet\Component\EntityPlugin;

/**
 * Wrapper around \ReflectionMethod to overload getDocComment to allow changes
 * to the @param/@return statements.
 *
 * Since Entities want to return the Generated interface they would add a
 * @return Generated\FooInterface.
 *
 * This @return statement is copied over to the generated interface that is
 * already in the Generated sub-namespace. Thus the Generated\ part should be
 * stripped.
 */
class ReflectionMethod
{
    private $method;

    public function __construct(\ReflectionMethod $method)
    {
        $this->method = $method;
    }

    public function getName()
    {
        return $this->method->getName();
    }

    public function getParameters()
    {
        return $this->method->getParameters();
    }

    public function isPublic()
    {
        return $this->method->isPublic();
    }

    public function isStatic()
    {
        return $this->method->isStatic();
    }

    public function getDocComment()
    {
        return preg_replace(
            '/@(return|param) Generated\\\\/',
            '@${1} ',
            $this->method->getDocComment()
        );
    }
}
