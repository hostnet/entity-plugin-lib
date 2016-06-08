<?php
namespace Hostnet\Component\EntityPlugin;

use phpunit\framework\TestCase;

/**
 * @covers Hostnet\Component\EntityPlugin\PackageClass
 */
class PackageClassTest extends TestCase
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

    public function testIsTrait()
    {
        $class         = 'Hostnet\\Component\\EntityPlugin\\Foo';
        $package_class = new PackageClass($class, new \SplFileInfo(__FILE__));
        $this->assertFalse($package_class->isTrait());

        $class         = 'Hostnet\\Component\\EntityPlugin\\FooTrait';
        $package_class = new PackageClass($class, new \SplFileInfo(__FILE__));
        $this->assertTrue($package_class->isTrait());
    }

    public function testIsInterface()
    {
        $class         = 'Hostnet\\Component\\EntityPlugin\\Foo';
        $package_class = new PackageClass($class, new \SplFileInfo(__FILE__));
        $this->assertFalse($package_class->isInterface());

        $class         = 'Hostnet\\Component\\EntityPlugin\\FooInterface';
        $package_class = new PackageClass($class, new \SplFileInfo(__FILE__));
        $this->assertTrue($package_class->isInterface());
    }

    public function testIsException()
    {
        $class         = 'Hostnet\\Component\\EntityPlugin\\Foo';
        $package_class = new PackageClass($class, new \SplFileInfo(__FILE__));
        $this->assertFalse($package_class->isException());

        $class         = 'Hostnet\\Component\\EntityPlugin\\FooException';
        $package_class = new PackageClass($class, new \SplFileInfo(__FILE__));
        $this->assertTrue($package_class->isException());
    }

    /**
     * @dataProvider getAliasProvider
     */
    public function testGetAlias($class, $expected)
    {
        $package_class = new PackageClass($class, __DIR__);
        $this->assertEquals($expected, $package_class->getAlias());
    }

    public function getAliasProvider()
    {
        return [
            [
                'Hihihi\Hahaha\Hohoho\Bluh',
                'HihihiHahahaHohoho'
            ],
            [
                'Hostnet\Client\Entity\Client',
                'HostnetClientEntity'
            ]
        ];
    }
}
