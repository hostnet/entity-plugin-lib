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
     *
     * @dataProvider generateProvider
     * @param EntityPackage $entity_package
     * @param WriterInterface $writer
     */
    public function testGenerate(
        EntityPackage $entity_package,
        WriterInterface $writer
    ) {
        $io          = $this->getMock('Composer\IO\IOInterface');
        $loader      = new \Twig_Loader_Filesystem(__DIR__ . '/../src/Resources/templates/');
        $environment = new \Twig_Environment($loader);
        $generator   = new CompoundGenerator($io, $environment, $entity_package, $writer);
        $generator->generate();
    }

    public function generateProvider()
    {
        // Test case 1: Empty test case
        $empty_package_content = new PackageContent([]);
        $writer_empty          = $this->mockWriter([]);

        $entities = [
            'Hostnet\Product\Entity\Product' => 'src/Entity/Product.php',
            'Hostnet\Contract\Entity\Contract' => 'src/Entity/Contract.php',
            'Hostnet\Contract\Entity\ContractWhenClientTrait' => 'src/Entity/ContractWhenClientTrait.php',
            'Hostnet\Contract\Entity\ContractWhenEasterTrait' => 'src/Entity/ContractWhenEasterTrait.php'
        ];
        $writes   = [
            'src/Entity/Generated/ProductTraits.php' => 'ProductTraits.php',
            'src/Entity/Generated/ContractTraits.php' => 'ContractTraits.php',
        ];

        $entity_package_content = new PackageContent($entities);
        $entity_package         = $this->mockEntityPackage($entity_package_content);

        $suggested_package_content = new PackageContent(['Hostnet\Client\Entity\Client' => 'src/Entity/Client.php']);
        $entity_package->addRequiredPackage($this->mockEntityPackage($suggested_package_content));

        $writer = $this->mockWriter($writes);

        return [
            [$this->mockEntityPackage($empty_package_content), $writer_empty],
            [$entity_package, $writer],
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
