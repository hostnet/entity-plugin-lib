<?php
/**
 * @copyright 2014-present Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Component\EntityPlugin;

use Composer\Package\Link;
use Composer\Package\Package;
use Composer\Semver\Constraint\Constraint;
use phpunit\framework\TestCase;

/**
 * @covers \Hostnet\Component\EntityPlugin\EntityPackageBuilder
 */
class EntityPackageBuilderTest extends TestCase
{
    /**
     * @dataProvider addsDependenciesProvider
     */
    public function testAddsDependencies(
        array $packages,
        array $expected_required_packages = [],
        array $expected_dependent_packages = []
    ): void {
        $mock = self::createMock('Hostnet\Component\EntityPlugin\PackagePathResolverInterface');
        $mock->expects($this->any())
             ->method('getSourcePath')
             ->will($this->returnValue(__DIR__));

        $builder         = new EntityPackageBuilder($mock, $packages);
        $entity_packages = $builder->getEntityPackages();

        self::assertTrue(is_array($entity_packages));

        foreach ($expected_required_packages as $package_name => $required_packages) {
            $actual_required = [];
            foreach ($entity_packages[$package_name]->getRequiredPackages() as $requirement) {
                $this->assertInstanceOf('Hostnet\Component\EntityPlugin\EntityPackage', $requirement);
                $actual_required[] = $requirement->getPackage();
            }
            $this->assertEquals($required_packages, $actual_required);
        }

        foreach ($expected_dependent_packages as $package_name => $dependent_packages) {
            $actual_dependant = [];
            foreach ($entity_packages[$package_name]->getDependentPackages() as $dep) {
                $this->assertInstanceOf('Hostnet\Component\EntityPlugin\EntityPackage', $dep);
                $actual_dependant[] = $dep->getPackage();
            }
            $this->assertEquals($dependent_packages, $actual_dependant);
        }
    }

    public function addsDependenciesProvider()
    {
        $requires_external = new Package('hostnet/requires-external', 1, 1);
        $requires_external->setRequires([
            new Link('hostnet/requires-external', 'hostnet/not-linked', new Constraint('=', '1')),
        ]);

        $foo    = new Package('hostnet/foo', 1, 1);
        $bar    = new Package('hostnet/bar', 1, 1);
        $foobar = new Package('hostnet/foobar', 1, 1);
        $bar->setRequires([
            new Link('hostnet/bar', 'hostnet/foo', new Constraint('=', '1')),
        ]);
        $foo->setSuggests([
            'hostnet/foobar' => 'Very useless text...',
        ]);

        return [
            [
                [],
            ],
            [
                [$requires_external],
            ],
            [
                [
                    $foo,
                ],
                [
                    'hostnet/foo' => [],
                ],
                [
                    'hostnet/foo' => [],
                ],
            ],
            [
                [
                    $foo,
                    $bar,
                    $foobar,
                ],
                [
                    'hostnet/foo' => [
                        $foobar,
                    ],
                    'hostnet/bar' => [
                        $foo,
                    ],
                ],
                [
                    'hostnet/foo' => [
                        $bar,
                    ],
                    'hostnet/bar' => [],
                ],
            ],
        ];
    }
}
