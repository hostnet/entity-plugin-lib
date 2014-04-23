<?php
namespace Hostnet\Component\EntityPlugin;

use Composer\Package\Link;
use Composer\Package\Package;

class EntityPackageTest extends \PHPUnit_Framework_TestCase
{

    public function testGetPackage()
    {
        $package        = new Package('hostnet/foo', 1.0, 1.0);
        $entity_package = new EntityPackage(
            $package,
            $this->getMock('Hostnet\Component\EntityPlugin\PackageIOInterface')
        );
        $this->assertEquals($package, $entity_package->getPackage());
    }

    public function testGetPackageIO()
    {
        $package_io     = $this->getMock('Hostnet\Component\EntityPlugin\PackageIOInterface');
        $entity_package = new EntityPackage(
            new Package('hostnet/foo', 1.0, 1.0),
            $package_io
        );
        $this->assertEquals($package_io, $entity_package->getPackageIO());
    }

    public function testGetRequires()
    {
        $package        = new Package('hostnet/foo', 1.0, 1.0);
        $entity_package = new EntityPackage(
            $package,
            $this->getMock('Hostnet\Component\EntityPlugin\PackageIOInterface')
        );
        $this->assertEquals(array(), $entity_package->getRequires());
        $link = new Link('hostnet/a', 'hostnet/foo');
        $package->setRequires(array(
            $link
        ));
        $this->assertEquals(array(
            $link
        ), $entity_package->getRequires());
    }

    public function testAddRequiredPackage()
    {
        $package        = new Package('hostnet/foo', 1.0, 1.0);
        $entity_package = new EntityPackage(
            $package,
            $this->getMock('Hostnet\Component\EntityPlugin\PackageIOInterface')
        );

        $child_a = new EntityPackage(
            new Package('hostnet/a', 1.0, 1.0),
            $this->getMock('Hostnet\Component\EntityPlugin\PackageIOInterface')
        );
        $child_b = new EntityPackage(
            new Package('hostnet/b', 1.0, 1.0),
            $this->getMock('Hostnet\Component\EntityPlugin\PackageIOInterface')
        );

        $this->assertEquals(array(), $entity_package->getRequiredPackages());

        $entity_package->addRequiredPackage($child_a);
        $this->assertEquals(array(
            $child_a
        ), $entity_package->getRequiredPackages());
        $entity_package->addRequiredPackage($child_b);
        $this->assertEquals(array(
            $child_a,
            $child_b
        ), $entity_package->getRequiredPackages());
    }

    public function testAddDependentPackage()
    {
        $package        = new Package('hostnet/foo', 1.0, 1.0);
        $entity_package = new EntityPackage(
            $package,
            $this->getMock('Hostnet\Component\EntityPlugin\PackageIOInterface')
        );

        $parent_a = new EntityPackage(
            new Package('hostnet/a', 1.0, 1.0),
            $this->getMock('Hostnet\Component\EntityPlugin\PackageIOInterface')
        );
        $parent_b = new EntityPackage(
            new Package('hostnet/b', 1.0, 1.0),
            $this->getMock('Hostnet\Component\EntityPlugin\PackageIOInterface')
        );
        $this->assertEquals(array(), $entity_package->getDependentPackages());
        $entity_package->addDependentPackage($parent_a);
        $this->assertEquals(array(
            $parent_a
        ), $entity_package->getDependentPackages());
        $entity_package->addDependentPackage($parent_b);
        $this->assertEquals(array(
            $parent_a,
            $parent_b
        ), $entity_package->getDependentPackages());
    }
}
