<?php
namespace Hostnet\Component\EntityPlugin;

class PackageIOTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider newPackageIOProvider
     *
     * @param \Iterator $iterator
     * @param array $entities
     * @param array $services
     * @param array $entity_traits
     * @param array $service_traits
     * @param array $generated_files
     */
    public function testNewPackageIO(
        array $class_map,
        array $entities = array(),
        array $services = array(),
        array $entity_traits = array(),
        array $service_traits = array(),
        array $generated_files = array()
    ) {
        $class_mapper = $this->mockClassMapper($class_map);
        $io           = new PackageIO(__DIR__, $class_mapper);
        $this->assertEquals(array_values($entities), $io->getEntities(), 'Entities');
        $this->assertEquals($services, $io->getServices(), 'Services');
        foreach (array_merge($entity_traits, $entities) as $name => $entity_trait) {
            $this->assertEquals($entity_trait, $io->getEntityOrEntityTrait($name), 'Entity or traits');
        }
        $this->assertEquals(array_values($entity_traits), $io->getEntityTraits(), 'Entity traits');
        $this->assertEquals($service_traits, $io->getServiceTraits(), 'Service traits');
        $this->assertEquals($generated_files, $io->getGeneratedFiles(), 'Generated files');
    }

    public function newPackageIOProvider()
    {
        $irrelevant_file      = ['Hostnet\Component\Foo' => 'foo.php'];
        $client_entity        = ['Foo\Client\Entity\Client' => 'src/Bar/Client.php'];
        $client_trait         = ['Foo\Entity\ClientTrait' => 'src/Bar/ClientTrait.php'];
        $client_service       = ['Foo\Service\ClientService' => 'src/Bar/ClientService.php'];
        $client_service_trait = ['Foo\Service\ClientServiceTrait' => 'src/Bar/ClientServiceTrait.php'];
        $ignored_files        = [
            'Hostnet\Entity\FooInterface' => 'FooInterface.php',
            'Hostnet\Entity\FooException' => 'FooException.php',
            'Hostnet\Service\FooInterface' => 'FooInterface.php',
            'Hostnet\Service\FooException' => 'FooException.php'
        ];

        $one_of_all = array_merge(
            $irrelevant_file,
            $client_trait,
            $client_service,
            $client_service_trait,
            $client_entity
        );

        $client_file               = new PackageClass('Foo\Client\Entity\Client', 'src/Bar/Client.php');
        $client_trait_file         = new PackageClass('Foo\Entity\ClientTrait', 'src/Bar/ClientTrait.php');
        $client_service_file       = new PackageClass('Foo\Service\ClientService', 'src/Bar/ClientService.php');
        $client_service_trait_file = new PackageClass(
            'Foo\Service\ClientServiceTrait',
            'src/Bar/ClientServiceTrait.php'
        );

        return [
            [
                []
            ],
            [
                $irrelevant_file
            ],
            [
                $ignored_files
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
                    'Client' => $client_file
                ],
                [
                    $client_service_file
                ],
                [
                    'Client' => $client_trait_file
                ],
                [
                    $client_service_trait_file
                ]
            ]
        ];
    }

    public function testGetEntityOrEntityTrait()
    {
        $io = new PackageIO(__DIR__, $this->mockClassMapper());
        $this->assertNull($io->getEntityOrEntityTrait('DoesNotExist'));
    }

    private function mockClassMapper(array $class_map = array())
    {
        $class_mapper = $this->getMock(
            'Hostnet\Component\EntityPlugin\ClassMapperInterface',
            array(
                'createClassMap'
            )
        );
        $class_mapper->expects($this->once())
            ->method('createClassMap')
            ->will($this->returnValue($class_map));
        return $class_mapper;
    }
}
