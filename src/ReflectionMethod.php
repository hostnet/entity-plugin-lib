<?php
/**
 * @copyright 2015-present Hostnet B.V.
 */
declare(strict_types=1);

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

    public function getName(): string
    {
        return $this->method->getName();
    }

    public function getParameters(): array
    {
        return array_map(
            function (\ReflectionParameter $parameter) {
                return new ReflectionParameter($parameter);
            },
            $this->method->getParameters()
        );
    }

    public function isPublic(): bool
    {
        return $this->method->isPublic();
    }

    public function isStatic(): bool
    {
        return $this->method->isStatic();
    }

    public function getReturnType(): ?ReflectionType
    {
        if (!method_exists($this->method, 'getReturnType')) {
            return null;
        }

        if ($type = $this->method->getReturnType()) {
            $reflection_type = new ReflectionType($type);

            // Self is not valid when used in different places.
            return false === \strpos($reflection_type->getName(), 'self') ? $reflection_type : null;
        }

        return null;
    }

    public function getDocComment(): string
    {
        $pattern = '/@(return|param|throws)\s+((?:[\$\w\\\\]+(?:\[\])?(?:\s*[|]\s*[\$\w\\\\]+(?:\[\])?)*))(\s|$)/';
        $comment = $this->method->getDocComment();

        if (!$comment) {
            return '';
        }

        return preg_replace_callback($pattern, [$this, 'processDocMatch'], $this->method->getDocComment());
    }

    /**
     * @param array $matches
     */
    private function processDocMatch(array $matches): string
    {
        $types = [];
        foreach (explode('|', $matches[2]) as $type) {
            $types[] = $this->qualifyType(trim($type));
        }

        return sprintf('@%s %s%s', $matches[1], implode('|', $types), $matches[3]);
    }

    /**
     * @param $type
     */
    private function qualifyType($type): string
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

    private function getDeclaringClass(): \ReflectionClass
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
     */
    private function getResolvedType($type): string
    {
        $context_factory = new ContextFactory();
        $context         = $context_factory->createFromReflector($this->getDeclaringClass());
        $fqn_resolver    = new FqsenResolver();

        return (string) $fqn_resolver->resolve($type, $context);
    }
}
