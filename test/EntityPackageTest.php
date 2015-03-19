<?php
namespace Hostnet\Component\EntityPlugin;

use Composer\Package\Link;
use Composer\Package\Package;

/**
 * @covers Hostnet\Component\EntityPlugin\EntityPackage
 */
class EntityPackageTest extends \PHPUnit_Framework_TestCase
{

    public function testGetPackage()
    {
        $package        = new Package('hostnet/foo', 1.0, 1.0);
        $entity_package = $this->createEntityPackage($package);
        $this->assertSame($package, $entity_package->getPackage());
    }

    public function testGetEntityContent()
    {
        $entity_content = $this->getMock('Hostnet\Component\EntityPlugin\PackageContentInterface');
        $entity_package  = new EntityPackage(
            new Package('hostnet/foo', 1.0, 1.0),
            $entity_content,
            $this->getMock('Hostnet\Component\EntityPlugin\PackageContentInterface')
        );
        $this->assertSame($entity_content, $entity_package->getEntityContent());
    }

    public function testGetRepositoryContent()
    {
        $repo_content = $this->getMock('Hostnet\Component\EntityPlugin\PackageContentInterface');
        $entity_package  = new EntityPackage(
            new Package('hostnet/foo', 1.0, 1.0),
            $this->getMock('Hostnet\Component\EntityPlugin\PackageContentInterface'),
            $repo_content
        );
        $this->assertSame($repo_content, $entity_package->getRepositoryContent());
    }

    public function testGetRequires()
    {
        $package        = new Package('hostnet/foo', 1.0, 1.0);
        $content        = $this->getMock('Hostnet\Component\EntityPlugin\PackageContentInterface');
        $entity_package = new EntityPackage($package, $content, $content);
        $this->assertEquals([], $entity_package->getRequires());
        $link = new Link('hostnet/a', 'hostnet/foo');
        $package->setRequires([$link]);
        $this->assertSame([$link], $entity_package->getRequires());
    }

    public function testGetSuggests()
    {
        $package        = new Package('hostnet/foo', 1.0, 1.0);
        $entity_package = $this->createEntityPackage($package);
        $this->assertEquals([], $entity_package->getSuggests());
        $link = new Link('hostnet/a', 'hostnet/foo');
        $package->setSuggests([
            $link
        ]);
        $this->assertEquals([
            $link
        ], $entity_package->getSuggests());
    }

    public function testAddRequiredPackage()
    {
        $package        = new Package('hostnet/foo', 1.0, 1.0);
        $entity_package = $this->createEntityPackage($package);

        $child_a = $this->createEntityPackage(new Package('hostnet/a', 1.0, 1.0));
        $child_b = $this->createEntityPackage(new Package('hostnet/b', 1.0, 1.0));

        $this->assertEquals([], $entity_package->getRequiredPackages());

        $entity_package->addRequiredPackage($child_a);
        $this->assertSame([
            $child_a
        ], $entity_package->getRequiredPackages());
        $entity_package->addRequiredPackage($child_b);
        $this->assertSame([
            $child_a,
            $child_b
        ], $entity_package->getRequiredPackages());
    }

    public function testAddDependentPackage()
    {
        $package        = new Package('hostnet/foo', 1.0, 1.0);
        $entity_package = $this->createEntityPackage($package);

        $parent_a = $this->createEntityPackage(new Package('hostnet/a', 1.0, 1.0));
        $parent_b = $this->createEntityPackage(new Package('hostnet/b', 1.0, 1.0));
        $this->assertEquals([], $entity_package->getDependentPackages());
        $entity_package->addDependentPackage($parent_a);
        $this->assertSame([
            $parent_a
        ], $entity_package->getDependentPackages());
        $entity_package->addDependentPackage($parent_b);
        $this->assertSame([
            $parent_a,
            $parent_b
        ], $entity_package->getDependentPackages());
    }

    public function testGetFlattenedRequiredPackages()
    {
        // Test case 1: Package with no required packages = empty list.
        $package_a = $this->createEntityPackage(new Package('hostnet/a', 1.0, 1.0));
        $this->assertEquals([], $package_a->getFlattenedRequiredPackages());

        // Test case 1: Package a depends on b. Package b depends on C.
        $package_b = $this->createEntityPackage(new Package('hostnet/b', 1.0, 1.0));
        $package_a->addRequiredPackage($package_b);

        $package_c = $this->createEntityPackage(new Package('hostnet/c', 1.0, 1.0));
        $package_b->addRequiredPackage($package_c);
        $expected = ['hostnet/b' => $package_b, 'hostnet/c' => $package_c];
        $this->assertSame($expected, $package_a->getFlattenedRequiredPackages());
    }

    private function createEntityPackage(Package $package)
    {
        // Lets change this to ::class after 14 Sep '15 once PHP 5.4 is unsupported.
        return new EntityPackage(
            $package,
            $this->getMock('Hostnet\Component\EntityPlugin\PackageContentInterface'),
            $this->getMock('Hostnet\Component\EntityPlugin\PackageContentInterface')
        );
    }
}
