<?php
namespace Hostnet\Component\EntityPlugin;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @see \Hostnet\Component\EntityPlugin\WriterInterface
 */
class Writer implements WriterInterface
{
    /**
     *
     * @see \Hostnet\Component\EntityPlugin\WriterInterface::writeFile()
     * @param string $path File path including directory
     * @param string $data File contents
     * @throws \Symfony\Component\Filesystem\Exception\IOException when directory can not be created or
     *         file is unwriteable
     */
    public function writeFile($path, $data)
    {
        $fs = new Filesystem();
        $fs->dumpFile($path, $data);
    }
}
