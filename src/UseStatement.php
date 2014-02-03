<?php
namespace Hostnet\Component\EntityPlugin;

/**
 * Represents a use statement
 *
 * @author Nico Schoenmaker <nschoenmaker@hostnet.nl>
 */
class UseStatement
{

    private $namespace;

    private $package_class;

    /**
     *
     * @param string $namespace
     * @param PackageClass $package_class
     */
    public function __construct($namespace, PackageClass $package_class)
    {
        $this->namespace     = $namespace;
        $this->package_class = $package_class;
    }

    /**
     *
     * @return boolean
     */
    public function isTrait()
    {
        return $this->package_class->isTrait();
    }

    /**
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     *
     * @return string
     */
    public function getAlias()
    {
        return str_replace('\\', '', $this->namespace);
    }
}
