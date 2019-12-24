<?php
namespace Hostnet\Component\EntityPlugin;

use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\Types\ContextFactory;

/**
 * Wrapper around \ReflectionMethod to overload getDocComment to allow changes
 * to the "@param"/"@return" statements.
 *
 * Since Entities want to return the Generated interface they would add a
 * "@return Generated\FooInterface".
 *
 * This "@return" statement is copied over to the generated interface that is
 * already in the Generated sub-namespace. Thus the Generated\ part should be
 * stripped.
 */
class ReflectionMethod
{
    private $method;

    /**
     * @param \ReflectionMethod $method
     */
    public function __construct(\ReflectionMethod $method)
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->method->getName();
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return array_map(
            function (\ReflectionParameter $parameter) {
                return new ReflectionParameter($parameter);
            },
            $this->method->getParameters()
        );
    }

    /**
     * @return bool
     */
    public function isPublic()
    {
        return $this->method->isPublic();
    }

    /**
     * @return bool
     */
    public function isStatic()
    {
        return $this->method->isStatic();
    }

    /**
     * @return ReflectionType|null
     */
    public function getReturnType()
    {
        if (!method_exists($this->method, 'getReturnType')) {
            return null;
        }

        if ($type = $this->method->getReturnType()) {
            // Self is not valid when used in different places.
            if ('self' === $type->__toString()) {
                return null;
            }

            return new ReflectionType($type);
        }

        return null;
    }

    /**
     * @return string
     */
    public function getDocComment()
    {
        $pattern = '/@(return|param|throws)\s+((?:[\$\w\\\\]+(?:\[\])?(?:\s*[|]\s*[\$\w\\\\]+(?:\[\])?)*))(\s|$)/';

        return preg_replace_callback($pattern, [$this, 'processDocMatch'], $this->method->getDocComment());
    }

    /**
     * @param array $matches
     * @return string
     */
    private function processDocMatch(array $matches)
    {
        $types = [];
        foreach (explode('|', $matches[2]) as $type) {
            $types[] = $this->qualifyType(trim($type));
        }

        return sprintf('@%s %s%s', $matches[1], implode('|', $types), $matches[3]);
    }

    /**
     * @param $type
     * @return string
     */
    private function qualifyType($type)
    {
        // "Type" is a variable, i.e. $this.
        if ('$' === $type[0]) {
            return $type;
        }

        // Type is fully qualified.
        if ('\\' === $type[0]) {
            return $type;
        }

        // Type is pointing to the Generated sub namespace.
        if (0 === strpos($type, 'Generated\\')) {
            return substr($type, 10);
        }

        // Handle array types
        $array = '';
        if ('[]' === substr($type, -2)) {
            $type  = substr($type, 0, -2);
            $array = '[]';
        }

        // Type can not be qualified.
        if (in_array($type, ReflectionTypeInterface::NON_QUALIFIED_TYPES, true)) {
            return $type . $array;
        }

        // Type resides in the namespace of the declaring class.
        return sprintf('%s%s', $this->getResolvedType($type), $array);
    }

    /**
     * @return \ReflectionClass
     */
    private function getDeclaringClass()
    {
        $method_filename = $this->method->getFileName();
        $declaring_class = $this->method->getDeclaringClass();
        foreach ($declaring_class->getTraits() as $trait) {
            if ($method_filename === $trait->getFileName()) {
                return $trait;
            }
        }

        return $declaring_class;
    }

    /**
     * @param $type
     * @return string
     */
    private function getResolvedType($type)
    {
        $context_factory = new ContextFactory();
        $context         = $context_factory->createFromReflector($this->getDeclaringClass());
        $fqn_resolver    = new FqsenResolver();

        return (string) $fqn_resolver->resolve($type, $context);
    }
}
