<?php
namespace Hostnet\Component\EntityPlugin\Compound;

use Composer\IO\IOInterface;
use Composer\Package\Package;
use Hostnet\Component\EntityPlugin\EntityPackage;
use Hostnet\Component\EntityPlugin\PackageContent;
use phpunit\framework\TestCase;
use Prophecy\Argument\Token\AnyValuesToken;
use Prophecy\Argument\Token\AnyValueToken;
use Symfony\Component\Filesystem\Filesystem;

/**
 * More of a functional-like test to check the outputted html.
 * @author Nico Schoenmaker <nschoenmaker@hostnet.nl>
 * @covers Hostnet\Component\EntityPlugin\Compound\CompoundGenerator
 */
class CompoundGeneratorTest extends TestCase
{
    /**
     * @dataProvider generateProvider
     * @param EntityPackage $entity_package
     * @param array $entity_fs_input
     * @param array $repo_fs_input
     */
    public function testGenerate(
        EntityPackage $entity_package,
        array $entity_fs_input,
        array $repo_fs_input
    ) {
        $io = $this->prophesize(IOInterface::class);
        $io->isDebug()->willReturn(true);
        $io->isVeryVerbose()->willReturn(true);
        if (count($entity_fs_input) || count($repo_fs_input)) {
            $io->write(new AnyValueToken())->shouldBeCalled();
        }
        $io = $io->reveal();

        $loader      = new \Twig_Loader_Filesystem(__DIR__ . '/../../src/Resources/templates/');
        $environment = new \Twig_Environment($loader);
        $provider    = new PackageContentProvider(PackageContent::ENTITY);
        $entity_fs   = $this->mockFilesystem($entity_fs_input);
        $generator   = new CompoundGenerator($io, $environment, $entity_fs, $provider);
        $generator->generate($entity_package);

        $provider = new PackageContentProvider(PackageContent::REPOSITORY);

        $repo_fs   = $this->mockFilesystem($repo_fs_input);
        $generator = new CompoundGenerator($io, $environment, $repo_fs, $provider);
        $generator->generate($entity_package);
    }

    public function generateProvider()
    {

        // Test case 2: Contract(repository) will be extended with client(repository)
        // While leaving Easter(repository) out

        $writes = [
            'src/Entity/Generated/ClientTrait.php' => 'ClientTrait.php',
            'src/Entity/Generated/ContractTrait.php' => 'ContractTrait.php',
            'src/Entity/Generated/ProductTrait.php' => 'ProductTrait.php',
            'src/Entity/Generated/DeathContractTrait.php' =>  'DeathContractTrait.php',
        ];

        $repo_writes = [
            'src/Repository/Generated/ContractRepositoryTrait.php' => 'ContractRepositoryTrait.php',
            'src/Repository/Generated/ProductRepositoryTrait.php' => 'ProductRepositoryTrait.php',
        ];

        $contract_class_map = [
                'Hostnet\Contract\Entity\Contract' => 'src/Entity/Contract.php',
                'Hostnet\Contract\Entity\DeathContract' => 'src/Entity/DeathContract.php',
                'Hostnet\Contract\Entity\ContractWhenClientTrait' => 'src/Entity/ContractWhenClientTrait.php',
                'Hostnet\Contract\Entity\ContractWhenEasterTrait' => 'src/Entity/ContractWhenEasterTrait.php',
                'Hostnet\Contract\Repository\ContractRepository' => 'src/Repository/ContractRepository.php',
                'Hostnet\Contract\Repository\ContractRepositoryWhenClientTrait' =>
                    'src/Repository/ContractRepositoryWhenClientTrait.php',
                'Hostnet\Contract\Repository\ContractRepositoryWhenEasterTrait' =>
                    'src/Repository/ContractRepositoryWhenEasterTrait.php',
            ];
        $contract_package   = $this->mockEntityPackage(
            $contract_class_map,
            'hostnet/contract'
        );

        $client_class_map = ['Hostnet\Client\Entity\Client' => 'src/Entity/Client.php'];
        $client_package   = $this->mockEntityPackage(
            $client_class_map,
            'hostnet/client'
        );

        $product_class_map = [
                'Hostnet\Product\Entity\Product' => 'src/Entity/Product.php',
                'Hostnet\Product\Repository\ProductRepository' => 'src/Repository/ProductRepository.php',
            ];
        $product_package   = $this->mockEntityPackage(
            $product_class_map,
            'hostnet/product'
        );

        $death_class_map = [
                'Hostnet\Death\Entity\DeathContractWhenProductTrait' => 'src/Entity/DeathContractWhenProductTrait.php',
                'Hostnet\Death\Entity\DeathContractWhenClientTrait' => 'src/Entity/DeathContractWhenClientTrait.php'
            ];
        $death_package   = $this->mockEntityPackage(
            $death_class_map,
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

        $app_package = $this->mockEntityPackage(
            array_merge(
                $contract_class_map,
                $client_class_map,
                $product_class_map,
                $death_class_map
            ),
            'hostnet/app'
        );

        $app_package->addRequiredPackage($contract_package);
        $app_package->addRequiredPackage($client_package);
        $app_package->addRequiredPackage($product_package);
        $app_package->addDependentPackage($death_package);


        $death_package->addRequiredPackage($contract_package);
        $death_package->addRequiredPackage($client_package);
        $death_package->addRequiredPackage($product_package);

        $death_package->addDependentPackage($app_package);

        $contract_package->addRequiredPackage($client_package);
        $contract_package->addRequiredPackage($product_package);

        return [
            [$this->mockEntityPackage([], 'hostnet/package'), [], []],
            [$app_package, $writes, $repo_writes],
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

    private function mockFilesystem(array $writes)
    {
        $filesystem = $this->prophesize(Filesystem::class);
        if (!count($writes)) {
            $filesystem->dumpFile(new AnyValuesToken())->shouldNotBeCalled();
        }
        foreach ($writes as $path => $fixture_file) {
            $filesystem->dumpFile(
                $path,
                file_get_contents(__DIR__ . '/CompoundEdgeCases/'. $fixture_file)
            )->shouldBeCalled();
        }
        return $filesystem->reveal();
    }
}
