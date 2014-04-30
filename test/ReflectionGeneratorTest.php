<?php
namespace Hostnet\Component\EntityPlugin;

use Composer\IO\NullIO;

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
     * @dataProvider generateProvider
     *
     * @param PackageClass $package_class
     */
    public function testGenerate(PackageClass $package_class)
    {
        include_once __DIR__ . '/EdgeCases/' . $package_class->getShortName() . '.php';
        $io          = new NullIO();
        $loader      = new \Twig_Loader_Filesystem(__DIR__ . '/../src/Resources/templates/');
        $environment = new \Twig_Environment($loader);

        $package_io = $this->getMock('Hostnet\Component\EntityPlugin\PackageIOInterface');

        $that = $this;
        $package_io->expects($this->once())
            ->method('writeGeneratedFile')
            ->will(
                $this->returnCallback(
                    function ($directory, $file, $data) use ($that, $package_class) {
                        $that->assertEquals($package_class->getGeneratedDirectory(), $directory);
                        $short_name = $package_class->getShortName();
                        if ($file === $short_name . 'Interface.php') {
                            $contents = file_get_contents(
                                __DIR__ . '/EdgeCases/' . $short_name . 'Interface.expected.php'
                            );
                        } elseif ($file === $short_name . 'TraitInterface.php') {
                            $contents = file_get_contents(
                                __DIR__ . '/EdgeCases/' . $short_name . 'TraitInterface.expected.php'
                            );
                        } else {
                            $this->fail('Unexpected file ' . $file);
                        }
                        $that->assertEquals($contents, $data);
                    }
                )
            );
        $generator = new ReflectionGenerator($io, $environment, $package_io, $package_class);
        $this->assertNull($generator->generate());
    }

    public function generateProvider()
    {
        return array(
            array(
                new PackageClass(
                    'Hostnet\EdgeCases\Entity\ConstructShouldNotBePresent',
                    __DIR__ . '/EdgeCases/ConstructShouldNotBePresent.php'
                )
            ),
            array(
                new PackageClass(
                    'Hostnet\EdgeCases\Entity\MultipleArguments',
                    __DIR__ . '/EdgeCases/MultipleArguments.php'
                )
            ),
            array(
                new PackageClass('Hostnet\EdgeCases\Entity\TypedParameters', __DIR__ . '/EdgeCases/TypedParameters.php')
            ),
            array(
                new PackageClass(
                    'Hostnet\EdgeCases\Entity\TraitsShouldRockTrait',
                    __DIR__ . '/EdgeCases/TraitsShouldRockTrait.php'
                )
            )
        );
    }
}
