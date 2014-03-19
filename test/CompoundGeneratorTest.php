<?php

use Hostnet\Component\EntityPlugin\CompoundGenerator;

use Composer\Package\Package;

use Hostnet\Component\EntityPlugin\EntityPackage;

/**
 * More of a functional-like test to check the outputted html.
 * @author Nico Schoenmaker <nschoenmaker@hostnet.nl>
 */
class CompoundGeneratorTest extends PHPUnit_Framework_TestCase
{
    public function testGenerate()
    {
        $io = $this->mockIo();
        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../src/Resources/templates/');
        $environment = new \Twig_Environment($loader);
        $generator = new CompoundGenerator($io, $environment, $this->mockEntityPackage());
    }

    /**
     * @return Composer\IO\IOInterface
     */
    private function mockIo()
    {
        return $this->getMock('Composer\IO\IOInterface');
    }

    private function mockEntityPackage()
    {
        $package = new Package('hostnet/package', '1.0.0', '1.0.0');
        $package_io = $this->getMock('Hostnet\Component\EntityPlugin\PackageIOInterface');
        return new EntityPackage($package, $package_io);
    }
}