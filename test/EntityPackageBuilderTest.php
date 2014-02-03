<?php
namespace Hostnet\Component\EntityPlugin;

use Composer\Package\Link;
use Composer\Package\Package;

class EntityPackageBuilderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider addsDependenciesProvider
     */
    public function testAddsDependencies(
        array $packages,
        array $expected_required_packages,
        array $expected_dependent_packages
    ) {
        $mock = $this->getMock('Hostnet\Component\EntityPlugin\PackagePathResolver');
        $mock->expects($this->any())
             ->method('getSourcePath')
             ->will($this->returnValue(__DIR__));

        $builder         = new EntityPackageBuilder($mock, $packages);
        $entity_packages = $builder->getEntityPackages();

        foreach ($expected_required_packages as $package_name => $required_packages) {
            $actual_required = array();
            foreach ($entity_packages[$package_name]->getRequiredPackages() as $requirement) {
                $this->assertInstanceOf('Hostnet\Component\EntityPlugin\EntityPackage', $requirement);
                $actual_required[] = $requirement->getPackage();
            }
            $this->assertEquals($required_packages, $actual_required);
        }

        foreach ($expected_dependent_packages as $package_name => $dependent_packages) {
            $actual_dependant = array();
            foreach ($entity_packages[$package_name]->getDependentPackages() as $dep) {
                $this->assertInstanceOf('Hostnet\Component\EntityPlugin\EntityPackage', $dep);
                $actual_dependant[] = $dep->getPackage();
            }
            $this->assertEquals($dependent_packages, $actual_dependant);
        }
    }

    public function addsDependenciesProvider()
    {
        $foo = new Package('hostnet/foo', 1, 1);
        $bar = new Package('hostnet/bar', 1, 1);
        $bar->setRequires(array(
            new Link('hostnet/bar', 'hostnet/foo')
        ));

        return array(
            array(
                array(),
                array(),
                array()
            ),
            array(
                array(
                    $foo
                ),
                array(
                    'hostnet/foo' => array()
                ),
                array(
                    'hostnet/foo' => array()
                )
            ),
            array(
                array(
                    $foo,
                    $bar
                ),
                array(
                    'hostnet/foo' => array(),
                    'hostnet/bar' => array(
                        $foo
                    )
                ),
                array(
                    'hostnet/foo' => array(
                        $bar
                    ),
                    'hostnet/bar' => array()
                )
            )
        );
    }
}
