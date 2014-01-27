<?php
use Hostnet\Component\EntityPlugin\UseStatement;

class UseStatementTest extends PHPUnit_Framework_TestCase
{

  /**
   * @dataProvider isTraitProvider
   */
  public function testIsTrait($file, $expected)
  {
    $file = $this->getMockBuilder('Symfony\Component\Finder\SplFileInfo')->disableOriginalConstructor()->getMock();
    $file->expects($this->once())->method('getFilename')->will($this->returnValue($file));
    $use_statement = new UseStatement('Meh', $file);
    $this->assertEquals($expected, $use_statement->isTrait());
  }

  public function isTraitProvider()
  {
    return array(
        array('Bla.php', false),
        array('TraitThis.php', false),
        array('ClientTrait.php', false)
        );
  }

  /**
   * @dataProvider getAliasProvider
   */
  public function testGetAlias($namespace, $expected)
  {
    $file = $this->getMockBuilder('Symfony\Component\Finder\SplFileInfo')->disableOriginalConstructor()->getMock();
    $use_statement = new UseStatement($namespace, $file);
    $this->assertEquals($expected, $use_statement->getAlias());
  }

  public function getAliasProvider()
  {
    return array(
        array('Hihihi\Hahaha\Hohoho', 'HihihiHahahaHohoho'),
        array('Hostnet\Client\Entity\Client', 'HostnetClientEntityClient'),
        );
  }
}
