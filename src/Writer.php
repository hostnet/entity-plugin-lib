<?php
namespace Hostnet\Component\EntityPlugin;

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
     */
    public function writeFile($path, $data)
    {
        $dir = dirname($path);
        $this->ensureDirectoryExists($dir);

        if (! is_dir($dir)) {
            mkdir($path, 0755, true);
        }
        file_put_contents($path, $data);
    }

    /**
     * Ensures that the Generated/ folder exists
     *
     * @throws \RuntimeException
     * @param string $path Directory Path
     */
    private function ensureDirectoryExists($path)
    {
        if (! is_dir($path) && ! mkdir($path)) {
            throw new \RuntimeException('Could not create "Generated" directory "' . $path . '"');
        }
    }
}
