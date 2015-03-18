<?php
namespace Hostnet\Component\EntityPlugin;

use Composer\IO\NullIO;
use Hostnet\EdgeCases\Entity\Generated\MultipleArgumentsTraitInterface;

/**
 * More a functiononal test then a unit-test
 *
 * Tests (minimized versions of) cases that we've found in real-life
 *
 * @author Nico Schoenmaker <nschoenmaker@hostnet.nl>
 */
class ReflectionGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateInIsolationProvider
     *
     * @param PackageClass $package_class
     */
    public function testGenerateInIsolation(PackageClass $package_class)
    {
        $short_name    = $package_class->getShortName();
        $class         = $package_class->getName();
        $base_dir      = __DIR__ . '/EdgeCases/';
        $expected_file = $base_dir . $short_name . 'Interface.expected.php';
        $actual_file   = $base_dir . 'Generated/' . $short_name . 'Interface.php';
        $expected      = file_get_contents($expected_file);

        ReflectionGenerator::generateInIsolation($class);

        $actual = file_get_contents($actual_file);
        unlink($actual_file);
        rmdir($base_dir . 'Generated/');

        $this->assertEquals($actual, $expected);
    }

    /**
     * @dataProvider generateProvider
     *
     * @param PackageClass $package_class
     */
    public function testGenerate(PackageClass $package_class)
    {
        //include_once __DIR__ . '/EdgeCases/' . $package_class->getShortName() . '.php';
        $loader      = new \Twig_Loader_Filesystem(__DIR__ . '/../src/Resources/templates/');
        $environment = new \Twig_Environment($loader);

        $package_io = $this->getMock('Hostnet\Component\EntityPlugin\WriterInterface');

        $that = $this;
        $package_io->expects($this->once())
            ->method('writeFile')
            ->will(
                $this->returnCallback(
                    function ($path, $data) use ($that, $package_class) {
                        $contents  = null;
                        $file      = basename($path);
                        $directory = dirname($path) . '/';

                        $that->assertEquals($package_class->getGeneratedDirectory(), $directory);

                        $short_name = $package_class->getShortName();

                        if ($file === $short_name . 'Interface.php') {
                            $contents = file_get_contents(
                                __DIR__ . '/EdgeCases/' . $short_name . 'Interface.expected.php'
                            );
                        } elseif ($file === $short_name . 'EntityTraitInterface.php') {
                            $contents = file_get_contents(
                                __DIR__ . '/EdgeCases/' . $short_name . 'EntityTraitInterface.expected.php'
                            );
                        } else {
                            $that->fail('Unexpected file ' . $file);
                        }
                        $that->assertEquals($contents, $data);
                    }
                )
            );
        $generator = new ReflectionGenerator($environment, $package_io, $package_class);
        $this->assertNull($generator->generate());
    }

    public function generateProvider()
    {
        return [
            [
                new PackageClass(
                    'Hostnet\EdgeCases\Entity\ConstructShouldNotBePresent',
                    __DIR__ . '/EdgeCases/ConstructShouldNotBePresent.php'
                )
            ],
            [
                new PackageClass(
                    'Hostnet\EdgeCases\Entity\MultipleArguments',
                    __DIR__ . '/EdgeCases/MultipleArguments.php'
                )
            ],
            [
                new PackageClass('Hostnet\EdgeCases\Entity\TypedParameters', __DIR__ . '/EdgeCases/TypedParameters.php')
            ]
        ];
    }

    public function generateInIsolationProvider()
    {
        return [
            [
                    new PackageClass(
                        'Hostnet\EdgeCases\Entity\MultipleArguments',
                        __DIR__ . '/EdgeCases/MultipleArguments.php'
                    )
                ],
                [
                    new PackageClass(
                        'Hostnet\EdgeCases\Entity\TypedParameters',
                        __DIR__ . '/EdgeCases/TypedParameters.php'
                    )
                ]
        ];
    }

    public function testMain()
    {
        // functionallity is already tested, test for smoke...
        $base_dir = __DIR__ . '/EdgeCases/Generated/';
        ReflectionGenerator::main('Hostnet\EdgeCases\Entity\MultipleArguments');

        unlink($base_dir . 'MultipleArgumentsInterface.php');
        rmdir($base_dir);
    }
}
