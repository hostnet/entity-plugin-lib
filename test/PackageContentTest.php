<?php
namespace Hostnet\Component\EntityPlugin;

class PackageContentTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider newPackageContentProvider
     *
     * @param array $class_map
     * @param array $entities
     * @param array $services
     * @param array $entity_traits
     * @param array $service_traits
     * @param array $optional_entity_traits
     */
    public function testNewPackageContent(
        array $class_map,
        array $entities = [],
        array $services = [],
        array $entity_traits = [],
        array $service_traits = [],
        array $optional_entity_traits = []
    ) {
        $content = new PackageContent($class_map);
        $this->assertEquals(array_values($entities), $content->getEntities(), 'Entities');
        $this->assertEquals($services, $content->getServices(), 'Services');
        foreach (array_merge($entity_traits, $entities) as $name => $entity_trait) {
            $this->assertEquals($entity_trait, $content->getEntityOrEntityTrait($name), 'Entity or traits');
        }
        $this->assertEquals(array_values($entity_traits), $content->getEntityTraits(), 'Entity traits');

        // test optional traits
        foreach (array_keys($entities) as $name) {
            if (!isset($optional_entity_traits[$name])) {
                $result = [];
            } else {
                $result = $optional_entity_traits[$name];
            }
            $this->assertEquals($result, $content->getOptionalEntityTraits($name), 'Optional entity traits');
        }

        $this->assertEquals($service_traits, $content->getServiceTraits(), 'Service traits');
    }

    public function newPackageContentProvider()
    {
        $irrelevant                 = ['Hostnet\Component\Foo' => 'foo.php'];
        $client_entity              = ['Foo\Client\Entity\Client' => 'src/Bar/Client.php'];
        $contract_entity            = ['Foo\Contract\Entity\Contract' => 'src/Bar/Contract.php'];
        $client_trait               = ['Foo\Entity\ClientTrait' => 'src/Bar/ClientTrait.php'];
        $contract_when_client_trait = ['Foo\Entity\ContractWhenClientTrait' => 'src/Bar/ContractWhenClientTrait.php'];
        $client_service             = ['Foo\Service\ClientService' => 'src/Bar/ClientService.php'];
        $client_service_trait       = ['Foo\Service\ClientServiceTrait' => 'src/Bar/ClientServiceTrait.php'];
        $ignored                    = [
            'Hostnet\Entity\FooInterface' => 'FooInterface.php',
            'Hostnet\Entity\FooException' => 'FooException.php',
            'Hostnet\Service\FooInterface' => 'FooInterface.php',
            'Hostnet\Service\FooException' => 'FooException.php'
        ];

        $one_of_all = array_merge(
            $irrelevant,
            $client_entity,
            $contract_entity,
            $client_trait,
            $contract_when_client_trait,
            $client_service,
            $client_service_trait
        );

        $client_file                     = new PackageClass('Foo\Client\Entity\Client', 'src/Bar/Client.php');
        $contract_file                   = new PackageClass('Foo\Contract\Entity\Contract', 'src/Bar/Contract.php');
        $client_trait_file               = new PackageClass('Foo\Entity\ClientTrait', 'src/Bar/ClientTrait.php');
        $contract_when_client_trait_file = new OptionalPackageTrait(
            'Foo\Entity\ContractWhenClientTrait',
            'src/Bar/ContractWhenClientTrait.php',
            'Client'
        );
        $client_service_file             = new PackageClass('Foo\Service\ClientService', 'src/Bar/ClientService.php');
        $client_service_trait_file       = new PackageClass(
            'Foo\Service\ClientServiceTrait',
            'src/Bar/ClientServiceTrait.php'
        );

        return [
            [
                []
            ],
            [
                $irrelevant
            ],
            [
                $ignored
            ],
            [
                $client_entity,
                [
                    'Client' => $client_file
                ]
            ],
            [
                $client_trait,
                [],
                [],
                [
                    'Client' => $client_trait_file
                ]
            ],
            [
                $one_of_all,
                [
                    'Client' => $client_file,
                    'Contract' => $contract_file
                ],
                [
                    $client_service_file
                ],
                [
                    'Client' => $client_trait_file,
                ],
                [
                    $client_service_trait_file
                ],
                [
                    'Contract' => [ $contract_when_client_trait_file ]
                ]
            ],
        ];
    }

    public function testGetEntityOrEntityTrait()
    {
        $io = new PackageContent((new ClassMapper())->createClassMap(__DIR__));
        $this->assertNull($io->getEntityOrEntityTrait('DoesNotExist'));
    }

    public function testHasEntityDoesCacheWarm()
    {
        $io = new PackageContent([]);
        $this->assertFalse($io->hasEntity('Foo'));
    }
}
