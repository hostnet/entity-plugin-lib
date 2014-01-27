<?php
use Hostnet\Component\EntityPlugin\PackageIO;

class PackageOITest extends PHPUnit_Framework_TestCase
{
  /**
   * @dataProvider __constructProvider
   * @param \Iterator $iterator
   * @param array $entities
   * @param array $services
   * @param array $entity_traits
   * @param array $service_traits
   * @param array $generated_files
   */
  public function test__construct(array $class_map, array $entities = array(), array $services = array(), array $entity_traits = array(), array $service_traits = array(), array $generated_files = array())
  {
    $class_mapper = $this->getMock('Hostnet\Component\EntityPlugin\ClassMapperInterface', array('createClassMap'));
    $class_mapper->expects($this->once())->method('createClassMap')->will($this->returnValue($class_map));
    $io = new PackageIO(__DIR__, $class_mapper);
    $this->assertEquals(array_values($entities), $io->getEntities(), 'Entities');
    $this->assertEquals($services, $io->getServices(), 'Services');
    foreach(array_merge($entities, $entity_traits) as $name => $entity_trait) {
      $this->assertEquals($entity_trait, $io->getEntityOrEntityTrait($name), 'Entity or traits');
    }
    $this->assertEquals(array_values($entity_traits), $io->getEntityTraits(), 'Entity traits');
    $this->assertEquals($service_traits, $io->getServiceTraits(), 'Service traits');
    $this->assertEquals($generated_files, $io->getGeneratedFiles(), 'Generated files');
  }

  public function __constructProvider()
  {
    $irrelevant_file = ['Hostnet\Component\Foo' => 'foo.php'];
    $client_entity = ['Foo\Client\Entity\Client' => 'src/Bar/Client.php'];
    $client_trait = ['Foo\Entity\ClientTrait' => 'ClientTrait.php'];
    $client_service = ['Foo\Service\ClientService' => 'ClientService.php'];
    $client_service_trait = ['Foo\Service\ServiceTrait' => 'ClientServiceTrait.php'];

    $one_of_all = array_merge($irrelevant_file, $client_trait, $client_service, $client_service_trait, $client_entity);

    $client_file = new \SplFileInfo('src/Bar/Client.php');
    $client_trait_file = new \SplFileInfo('src/Bar/ClientTrait.php');
    $client_service_file = new \SplFileInfo('src/Bar/Client.php');
    $client_service_trait_file = new \SplFileInfo('src/Bar/Client.php');

    return [
        [[]],
        [$irrelevant_file],
        array($client_entity, array('Client' => $client_file)),
        array($one_of_all, array('Client' => $client_file), array($client_service_file), array('Client' => $client_trait_file), array($client_service_trait_file))
    ];
  }

  private function mockFileInfo($relative_path, $basename)
  {
    $file_info = $this->getMockBuilder('Symfony\Component\Finder\SplFileInfo')->disableOriginalConstructor()->setMethods(array('getRelativePath', 'getBasename', '__toString'))->getMock();
    $file_info->expects($this->any())->method('getRelativePath')->will($this->returnValue($relative_path));
    $file_info->expects($this->any())->method('getBasename')->will($this->returnValue($basename));
    $file_info->expects($this->any())->method('__toString')->will($this->returnValue('PackageIOTestMock: '.$relative_path.';'.$basename));

    return $file_info;
  }
}