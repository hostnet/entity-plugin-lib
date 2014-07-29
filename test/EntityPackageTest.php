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
            $this->getMock('Hostnet\Component\EntityPlugin\PackageContentInterface')
        );
        $this->assertEquals($package, $entity_package->getPackage());
    }

    public function testGetPackageContent()
    {
        $package_io     = $this->getMock('Hostnet\Component\EntityPlugin\PackageContentInterface');
        $entity_package = new EntityPackage(
            new Package('hostnet/foo', 1.0, 1.0),
            $package_io
        );
        $this->assertEquals($package_io, $entity_package->getPackageContent());
    }

    public function testGetRequires()
    {
        $package        = new Package('hostnet/foo', 1.0, 1.0);
        $entity_package = new EntityPackage(
            $package,
            $this->getMock('Hostnet\Component\EntityPlugin\PackageContentInterface')
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

    public function testGetSuggests()
    {
        $package        = new Package('hostnet/foo', 1.0, 1.0);
        $entity_package = new EntityPackage(
            $package,
            $this->getMock('Hostnet\Component\EntityPlugin\PackageContentInterface')
        );
        $this->assertEquals(array(), $entity_package->getSuggests());
        $link = new Link('hostnet/a', 'hostnet/foo');
        $package->setSuggests(array(
            $link
        ));
        $this->assertEquals(array(
            $link
        ), $entity_package->getSuggests());
    }

    public function testAddRequiredPackage()
    {
        $package        = new Package('hostnet/foo', 1.0, 1.0);
        $entity_package = new EntityPackage(
            $package,
            $this->getMock('Hostnet\Component\EntityPlugin\PackageContentInterface')
        );

        $child_a = new EntityPackage(
            new Package('hostnet/a', 1.0, 1.0),
            $this->getMock('Hostnet\Component\EntityPlugin\PackageContentInterface')
        );
        $child_b = new EntityPackage(
            new Package('hostnet/b', 1.0, 1.0),
            $this->getMock('Hostnet\Component\EntityPlugin\PackageContentInterface')
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
            $this->getMock('Hostnet\Component\EntityPlugin\PackageContentInterface')
        );

        $parent_a = new EntityPackage(
            new Package('hostnet/a', 1.0, 1.0),
            $this->getMock('Hostnet\Component\EntityPlugin\PackageContentInterface')
        );
        $parent_b = new EntityPackage(
            new Package('hostnet/b', 1.0, 1.0),
            $this->getMock('Hostnet\Component\EntityPlugin\PackageContentInterface')
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

    public function testGetFlattenedRequiredPackages()
    {
        // Test case 1: Package with no required packages = empty list.
        $package_a = new EntityPackage(
            new Package('hostnet/a', 1.0, 1.0),
            $this->getMock('Hostnet\Component\EntityPlugin\PackageContentInterface'));
        $this->assertEquals([], $package_a->getFlattenedRequiredPackages());

        // Test case 1: Package a depends on b. Package b depends on C.
        $package_b = new EntityPackage(
                new Package('hostnet/b', 1.0, 1.0),
                $this->getMock('Hostnet\Component\EntityPlugin\PackageContentInterface'));
        $package_a->addRequiredPackage($package_b);

        $package_c = new EntityPackage(
                new Package('hostnet/c', 1.0, 1.0),
                $this->getMock('Hostnet\Component\EntityPlugin\PackageContentInterface'));
        $package_b->addRequiredPackage($package_c);
        $expected = ['hostnet/b' => $package_b, 'hostnet/c' => $package_c];
        $this->assertEquals($expected, $package_a->getFlattenedRequiredPackages());
    }
}
