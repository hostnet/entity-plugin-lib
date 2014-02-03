<?php
namespace Hostnet\Component\EntityPlugin;

class PackageClassTest extends \PHPUnit_Framework_TestCase
{

    public function testGetName()
    {
        $class         = 'Hostnet\\Component\\EntityPlugin\\Foo';
        $package_class = new PackageClass($class, new \SplFileInfo(__FILE__));
        $this->assertEquals($class, $package_class->getName());
    }

    public function testGetShortName()
    {
        $class         = 'Hostnet\\Component\\EntityPlugin\\Foo';
        $package_class = new PackageClass($class, new \SplFileInfo(__FILE__));
        $this->assertEquals('Foo', $package_class->getShortName());
    }

    public function testGetGeneratedDirectory()
    {
        $package_class = new PackageClass('Foo', '/bar/src/Foo.php');
        $this->assertEquals('/bar/src/Generated/', $package_class->getGeneratedDirectory());
    }

    public function testGetGeneratedNamespaceName()
    {
        $class         = 'Hostnet\\Component\\EntityPlugin\\Foo';
        $package_class = new PackageClass($class, new \SplFileInfo(__FILE__));
        $this->assertEquals('Hostnet\\Component\\EntityPlugin\\Generated', $package_class->getGeneratedNamespaceName());
    }
}
