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
     * @param PackageContentInterface $package_io
     */
    public function testGenerate(
        PackageContentInterface $package_content,
        WriterInterface $writer
    ) {
        $io          = $this->getMock('Composer\IO\IOInterface');
        $loader      = new \Twig_Loader_Filesystem(__DIR__ . '/../src/Resources/templates/');
        $environment = new \Twig_Environment($loader);

        // 1. A basic run with no entities
        $entity_package = $this->mockEntityPackage($package_content);
        $generator      = new CompoundGenerator($io, $environment, $entity_package, $writer);
        $generator->generate();
    }

    public function generateProvider()
    {
        $empty_package_content = new PackageContent([]);
        $entities = [
            'Hostnet\Product\Entity\Product' => 'src/Entity/Product.php',
            'Hostnet\Client\Entity\Client' => 'src/Entity/Client.php',
            'Hostnet\Contract\Entity\Contract' => 'src/Entity/Contract.php',
            'Hostnet\Contract\Entity\ContractWhenClientTrait' => 'src/Entity/ContractWhenClientTrait.php',
            'Hostnet\Contract\Entity\ContractWhenEasterTrait' => 'src/Entity/ContractWhenEasterTrait.php'
        ];
        $writes = [
            'src/Entity/Generated/ProductTraits.php' => 'ProductTraits.php',
            'src/Entity/Generated/ProductInterface.php' => 'ProductInterface.php',
            'src/Entity/Generated/ClientTraits.php' => 'ClientTraits.php',
            'src/Entity/Generated/ClientInterface.php' => 'ClientInterface.php',
            'src/Entity/Generated/ContractTraits.php' => 'ContractTraits.php',
            'src/Entity/Generated/ContractInterface.php' => 'ContractInterface.php',
        ];

        $entity_package_content = new PackageContent($entities);

        $writer_empty = $this->mockWriter([]);
        $writer       = $this->mockWriter($writes);

        return [
            [$empty_package_content, $writer_empty],
            [$entity_package_content, $writer],
        ];
    }

    private function mockEntityPackage(PackageContentInterface $package_content)
    {
        $package        = new Package('hostnet/package', '1.0.0', '1.0.0');
        $entity_package = new EntityPackage($package, $package_content);

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
}
