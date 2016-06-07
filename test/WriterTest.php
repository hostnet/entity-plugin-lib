<?php
namespace Hostnet\Component\EntityPlugin;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @covers Hostnet\Component\EntityPlugin\Writer
 */
class WriterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Simple file (full path) for ensuring the writing works.
     *
     * @var string
     */
    private $test_file;


    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->test_file = tempnam(sys_get_temp_dir(), 'entity-plugin-lib-unit-test');
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        $fs = new Filesystem();
        $fs->remove($this->test_file);
    }

    /**
     * Test the write operation.
     */
    public function testWrite()
    {
        $writer = new Writer();
        $writer->writeFile($this->test_file, 'Aaapie');
        self::assertEquals('Aaapie', file_get_contents($this->test_file));
    }

    /**
     * That that is throws an IOException when fs operations fail.
     * @expectedException \Symfony\Component\Filesystem\Exception\IOException
     */
    public function testCanNotCreateDir()
    {
        $writer = new Writer();
        $writer->writeFile($this->test_file . '/afile_in_a_sub_dir_that_is_a_file', 'Aaapie');
    }
}
