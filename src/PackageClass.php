<?php
/**
 * @copyright 2014-present Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Component\EntityPlugin;

/**
 * This models the distinction between the class name and the generated location
 */
class PackageClass
{
    private $class;

    private $path;

    /**
     * @param string $class
     * @param string $path
     */
    public function __construct($class, $path)
    {
        $this->class = $class;
        $this->path  = $path;
    }

    /**
     * @see \ReflectionClass::getName()
     */
    public function getName(): string
    {
        return $this->class;
    }

    /**
     * @see \ReflectionClass::getShortName()
     */
    public function getShortName(): string
    {
        $pos = strrpos($this->class, '\\');
        return substr($this->class, $pos + 1);
    }

    /**
     * @return string The folder in which to generate the files
     */
    public function getGeneratedDirectory(): string
    {
        $pos  = strrpos($this->path, '/');
        $path = substr($this->path, 0, $pos);
        return $path . '/Generated/';
    }

    /**
     * @see \ReflectionClass::getNamespaceName()
     * @return string The namespace name of the to-be-generated classes
     */
    public function getGeneratedNamespaceName(): string
    {
        return $this->getNamespaceName() . '\Generated';
    }

    public function getNamespaceName(): string
    {
        $pos = strrpos($this->class, '\\');
        return substr($this->class, 0, $pos);
    }

    public function getAlias(): string
    {
        return str_replace('\\', '', $this->getNamespaceName());
    }

    /**
     * Is this a trait? Assumes you have your naming in order.
     */
    public function isTrait(): bool
    {
        return $this->endsWith($this->getShortName(), 'Trait');
    }

    /**
     * Is this an interface? Assumes you have your naming in order.
     */
    public function isInterface(): bool
    {
        return $this->endsWith($this->getShortName(), 'Interface');
    }

    /**
     * Is this an excepiton? Assumes you have your naming in order.
     */
    public function isException(): bool
    {
        return $this->endsWith($this->getShortName(), 'Exception');
    }

    private function endsWith($haystack, $needle): bool
    {
        if ($needle === '' || substr($haystack, - strlen($needle)) === $needle) {
            return true;
        } else {
            return false;
        }
    }
}
