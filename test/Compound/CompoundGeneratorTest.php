<?php
namespace Hostnet\Component\EntityPlugin\Compound;

use Composer\Package\Package;
use Hostnet\Component\EntityPlugin\EntityPackage;
use Hostnet\Component\EntityPlugin\WriterInterface;
use Hostnet\Component\EntityPlugin\PackageContent;

/**
 * More of a functional-like test to check the outputted html.
 * @author Nico Schoenmaker <nschoenmaker@hostnet.nl>
 * @covers Hostnet\Component\EntityPlugin\Compound\CompoundGenerator
 */
class CompoundGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateProvider
     * @param EntityPackage $entity_package
     * @param WriterInterface $entity_writer
     * @param WriterInterface $repo_writer
     */
    public function testGenerate(
        EntityPackage $entity_package,
        WriterInterface $entity_writer,
        WriterInterface $repo_writer
    ) {
        $io          = $this->getMock('Composer\IO\IOInterface');
        $loader      = new \Twig_Loader_Filesystem(__DIR__ . '/../../src/Resources/templates/');
        $environment = new \Twig_Environment($loader);
        $provider    = new PackageContentProvider(PackageContent::ENTITY);
        $generator   = new CompoundGenerator($io, $environment, $entity_writer, $provider);
        $generator->generate($entity_package);

        $provider  = new PackageContentProvider(PackageContent::REPOSITORY);
        $generator = new CompoundGenerator($io, $environment, $repo_writer, $provider);
        $generator->generate($entity_package);
    }

    public function generateProvider()
    {
        // Test case 1: Empty test case
        $writer_empty = $this->mockWriter([]);

        // Test case 2: Contract(repository) will be extended with client(repository)
        // While leaving Easter(repository) out

        $writes = [
            'src/Entity/Generated/ContractTraits.php' => 'ContractTraits.php',
            'src/Entity/Generated/ProductTraits.php' => 'ProductTraits.php',
            'src/Entity/Generated/DeathContractTraits.php' =>  'DeathContractTraits.php',
        ];

        $repo_writes = [
            'src/Repository/Generated/ContractRepositoryTraits.php' => 'ContractRepositoryTraits.php',
            'src/Repository/Generated/ProductRepositoryTraits.php' => 'ProductRepositoryTraits.php',
        ];

        $contract_package = $this->mockEntityPackage(
            [
                'Hostnet\Contract\Entity\Contract' => 'src/Entity/Contract.php',
                'Hostnet\Contract\Entity\DeathContract' => 'src/Entity/DeathContract.php',
                'Hostnet\Contract\Entity\ContractWhenClientTrait' => 'src/Entity/ContractWhenClientTrait.php',
                'Hostnet\Contract\Entity\ContractWhenEasterTrait' => 'src/Entity/ContractWhenEasterTrait.php',
                'Hostnet\Contract\Repository\ContractRepository' => 'src/Repository/ContractRepository.php',
                'Hostnet\Contract\Repository\ContractRepositoryWhenClientTrait' =>
                    'src/Repository/ContractRepositoryWhenClientTrait.php',
                'Hostnet\Contract\Repository\ContractRepositoryWhenEasterTrait' =>
                    'src/Repository/ContractRepositoryWhenEasterTrait.php',
            ],
            'hostnet/contract'
        );

        $client_package = $this->mockEntityPackage(
            ['Hostnet\Client\Entity\Client' => 'src/Entity/Client.php'],
            'hostnet/client'
        );

        $product_package = $this->mockEntityPackage(
            [
                'Hostnet\Product\Entity\Product' => 'src/Entity/Product.php',
                'Hostnet\Product\Repository\ProductRepository' => 'src/Repository/ProductRepository.php',
            ],
            'hostnet/product'
        );

        $death_package = $this->mockEntityPackage(
            [
                'Hostnet\Death\Entity\DeathContractWhenProductTrait' => 'src/Entity/DeathContractWhenProductTrait.php',
                'Hostnet\Death\Entity\DeathContractWhenClientTrait' => 'src/Entity/DeathContractWhenClientTrait.php'
            ],
            'hostnet/death'
        );

        /*
         * APP -------req----------------------------> CONTRACT
         * | | \                                      /   |  ^
         * | |  \-----req-------------> CLIENT <-sug-/    |  |
         * | |                             ^              |  |
         * |  \                            |              /  /
         * |   \------req---> PRODUCT <-------sug--------/  /
         * |                   ^          /                /
         * |                   |   /-sug-/                /
         * |                  sug /                      /
         * \                   | /                      /
         *  \---------req---> DEATH --sug--- ----------/
         */

        $app_package = $this->mockEntityPackage([], 'hostnet/app');

        $app_package->addRequiredPackage($contract_package);
        $app_package->addRequiredPackage($client_package);
        $app_package->addRequiredPackage($product_package);
        $app_package->addRequiredPackage($death_package);

        $death_package->addRequiredPackage($contract_package);
        $death_package->addRequiredPackage($client_package);
        $death_package->addRequiredPackage($product_package);

        $contract_package->addRequiredPackage($client_package);
        $contract_package->addRequiredPackage($product_package);

        $writer      = $this->mockWriter($writes);
        $repo_writer = $this->mockWriter($repo_writes);

        return [
            [$this->mockEntityPackage([], 'hostnet/package'), $writer_empty, $writer_empty],
            [$app_package, $writer, $repo_writer],
        ];
    }

    private function mockEntityPackage(array $class_map, $name)
    {
        $package        = new Package($name, '1.0.0', '1.0.0');
        $entity_content = new PackageContent($class_map, PackageContent::ENTITY);
        $repo_content   = new PackageContent($class_map, PackageContent::REPOSITORY);
        $entity_package = new EntityPackage($package, $entity_content, $repo_content);

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
