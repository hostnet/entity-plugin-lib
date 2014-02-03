<?php
namespace Hostnet\Component\EntityPlugin;

/**
 * Interface representing all file reads and writes to a package
 * So we can mock out the file IO during unit-tests
 *
 * @author Nico Schoenmaker <nschoenmaker@hostnet.nl>
 */
interface PackageIOInterface
{

    /**
     * Get the entities contained in the package
     *
     * @return PackageClass[]
     */
    public function getEntities();

    /**
     * Get an entity, or entity trait by name, provided it exists in this package
     *
     * @param string $name
     * @return PackageClass null
     */
    public function getEntityOrEntityTrait($name);

    /**
     * Get all entity traits in the package
     *
     * @return PackageClass[]
     */
    public function getEntityTraits();

    /**
     * Get the repositories contained in the package
     *
     * @return PackageClass[]
     */
    public function getServices();

    /**
     * Get the repository traits contained in the package
     *
     * @return PackageClass[]
     */
    public function getServiceTraits();

    /**
     * Get the generated files contained in the package
     *
     * @return PackageClass[]
     */
    public function getGeneratedFiles();

    /**
     * Write a generated file to the package.
     * Ensures by itself that the directory exists
     *
     * @param $path The
     *            path the file should be generated to
     * @param $file The
     *            filename
     * @param $data The
     *            data to write
     */
    public function writeGeneratedFile($path, $file, $data);
}
