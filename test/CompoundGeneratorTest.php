<?php
namespace Hostnet\Component\EntityPlugin;

use Composer\Package\Package;

/**
 * More of a functional-like test to check the outputted html.
 * @author Nico Schoenmaker <nschoenmaker@hostnet.nl>
 */
class CompoundGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateProvider
     * @param PackageIOInterface $package_io
     * @param array $dependant_packages
     */
    public function testGenerate(
        PackageIOInterface $package_io,
        array $dependant_packages,
        WriterInterface $writer
    ) {
        $io          = $this->mockIo();
        $loader      = new \Twig_Loader_Filesystem(__DIR__ . '/../src/Resources/templates/');
        $environment = new \Twig_Environment($loader);

        // 1. A basic run with no entities
        $entity_package = $this->mockEntityPackage($package_io, $dependant_packages);
        $generator      = new CompoundGenerator($io, $environment, $entity_package, $writer);
        $generator->generate();
    }

    public function generateProvider()
    {
        $entities         = [];
        $empty_package_io = $this->mockPackageIO($entities);

        $entities = [new PackageClass('Hostnet\Product\Entity\Product', 'src/Entity/')];

        $writes = [
            'src/Entity/Generated/ProductTraits.php' => 'SingleEntityTraits.php',
            'src/Entity/Generated/ProductInterface.php' => 'ProductInterface.php'
        ];

        $one_entity_package_io = $this->mockPackageIO($entities);

        $writer_empty = $this->mockWriter([]);
        $writer_one   = $this->mockWriter($writes);

        return [
            [$empty_package_io, [], $writer_empty],
            [$one_entity_package_io, [], $writer_one],
        ];
    }

    /**
     * @return Composer\IO\IOInterface
     */
    private function mockIo()
    {
        return $this->getMock('Composer\IO\IOInterface');
    }

    private function mockEntityPackage(PackageIOInterface $package_io, array $dependant_packages = [])
    {
        $package        = new Package('hostnet/package', '1.0.0', '1.0.0');
        $entity_package = new EntityPackage($package, $package_io);

        foreach ($dependant_packages as $dependant_package) {
            $entity_package->addDependentPackage($dependant_package);
        }

        return $entity_package;
    }

    private function mockWriter(array $writes)
    {
        $writer = $this->getMock('Hostnet\Component\EntityPlugin\WriterInterface');
        $that   = $this;
        $writer->expects($this->exactly(count($writes)))
        ->method('writeFile')
        ->will($this->returnCallback(function ($path, $data) use ($writes, $that) {
            $that->assertTrue(isset($writes[$path]), 'No write expected to ' . $path);
            $contents = file_get_contents(__DIR__ . '/CompoundEdgeCases/'.$writes[$path]);
            $that->assertEquals($contents, $data);
        }));

        return $writer;
    }

    private function mockPackageIO(array $entities, array $known_traits = [])
    {
        $package_io = $this->getMock('Hostnet\Component\EntityPlugin\PackageIOInterface');
        $package_io->expects($this->any())
            ->method('getEntities')
            ->will($this->returnValue($entities));

        foreach ($entities as $entity) {
            $known_traits[$entity->getShortName()] = $entity;
        }

        $package_io->expects($this->any())
            ->method('getEntityOrEntityTrait')
            ->will($this->returnCallback(function ($name) use ($known_traits) {
                if (isset($known_traits[$name])) {
                    return $known_traits[$name];
                } else {
                    return null;
                }
            }));
        return $package_io;
    }
}
