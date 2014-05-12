<?php
namespace Hostnet\Component\EntityPlugin;

/**
 * Write files to disk and make sure directory exists.
 */
interface WriterInterface
{
    /**
     * Write a generated file to the package.
     * Ensures by itself that the directory exists
     *
     * @param $path The
     *            path the file should be generated to
     * @param $data The
     *            data to write
     */
    public function writeFile($path, $data);
}
