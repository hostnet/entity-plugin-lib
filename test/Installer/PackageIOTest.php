<?php
use Hostnet\Entities\Installer\PackageIO;

class PackageOITest extends PHPUnit_Framework_TestCase
{
  /**
   * @dataProvider __constructProvider
   * @param \Iterator $iterator
   * @param array $entities
   * @param array $repositories
   * @param array $entity_traits
   * @param array $repository_traits
   * @param array $generated_files
   */
  public function test__construct(\Iterator $iterator, array $entities = array(), array $repositories = array(), array $entity_traits = array(), array $repository_traits = array(), array $generated_files = array())
  {
    $finder = $this->getMock('Symfony\Component\Finder\Finder', array('getIterator'));
    $finder->expects($this->once())->method('getIterator')->will($this->returnValue($iterator));
    $io = new PackageIO($finder, __DIR__);
    $this->assertEquals($entities, $io->getEntities());
    $this->assertEquals($repositories, $io->getRepositories());
    foreach($entity_traits as $name => $entity_trait) {
      $this->assertEquals($entity_trait, $io->getEntityTrait($name));
    }
    $this->assertEquals($repository_traits, $io->getRepositoryTraits());
    $this->assertEquals($generated_files, $io->getGeneratedFiles());
  }

  public function __constructProvider()
  {
    $irrelevant_file = $this->mockFileInfo('foo', 'meh');
    $client_entity = $this->mockFileInfo('Foo/Entities/Client.php', 'Client.php');
    $client_trait = $this->mockFileInfo('Foo/Entities/ClientTrait.php', 'ClientTrait.php');
    $client_repository = $this->mockFileInfo('Foo/Entities/ClientRepository.php', 'ClientRepository.php');
    $client_repository_trait = $this->mockFileInfo('Foo/Entities/ClientRepositoryTrait.php', 'ClientRepositoryTrait.php');
    $one_entity = new ArrayIterator(array($client_entity));

    $one_of_all = new ArrayIterator(array($irrelevant_file, $client_entity, $client_trait, $client_repository, $client_repository_trait));

    return array(
        array(new ArrayIterator(array())),
        array(new ArrayIterator(array($irrelevant_file))),
        array($one_entity, array($client_entity)),
        array($one_of_all, array($client_entity), array($client_repository), array('Client' => $client_trait), array($client_repository_trait))
        );
  }

  private function mockFileInfo($relative_path, $basename)
  {
    $file_info = $this->getMockBuilder('SplFileInfo')->disableOriginalConstructor()->setMethods(array('getRelativePath', 'getBasename', '__toString'))->getMock();
    $file_info->expects($this->any())->method('getRelativePath')->will($this->returnValue($relative_path));
    $file_info->expects($this->any())->method('getBasename')->will($this->returnValue($basename));
    $file_info->expects($this->any())->method('__toString')->will($this->returnValue('PackageIOTestMock: '.$relative_path.';'.$basename));

    return $file_info;
  }
}