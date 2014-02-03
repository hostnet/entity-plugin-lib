<?php
namespace Hostnet\Component\EntityPlugin;

/**
 * This models the distinction between the class name and the generated location
 */
class PackageClass
{

    private $class;

    private $path;

    /**
     *
     * @param string $class
     * @param string $path
     */
    public function __construct($class, $path)
    {
        $this->class = $class;
        $this->path  = $path;
    }

    /**
     *
     * @see \ReflectionClass::getName()
     */
    public function getName()
    {
        return $this->class;
    }

    /**
     *
     * @see \ReflectionClass::getShortName()
     */
    public function getShortName()
    {
        $pos = strrpos($this->class, '\\');
        return substr($this->class, $pos + 1);
    }

    /**
     *
     * @return string The folder in which to generate the files
     */
    public function getGeneratedDirectory()
    {
        $pos  = strrpos($this->path, '/');
        $path = substr($this->path, 0, $pos);
        return $path . '/Generated/';
    }

    /**
     *
     * @see \ReflectionClass::getNamespaceName()
     * @return string The namespace name of the to-be-generated classes
     */
    public function getGeneratedNamespaceName()
    {
        return $this->getNamespaceName() . '\Generated';
    }

    public function getNamespaceName()
    {
        $pos = strrpos($this->class, '\\');
        return substr($this->class, 0, $pos);
    }

    /**
     *
     * @return boolean
     */
    public function isTrait()
    {
        return $this->endsWith($this->getShortName(), 'Trait');
    }

    private function endsWith($haystack, $needle)
    {
        if ($needle === '' || substr($haystack, - strlen($needle)) === $needle) {
            return true;
        } else {
            return false;
        }
    }
}
