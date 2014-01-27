<?php
use Hostnet\Component\EntityPlugin\PackageClass;

use Hostnet\Component\EntityPlugin\UseStatement;

class UseStatementTest extends PHPUnit_Framework_TestCase
{

  /**
   * @dataProvider isTraitProvider
   */
  public function testIsTrait($class, $expected)
  {
    $package_class = new PackageClass($class, __DIR__);
    $use_statement = new UseStatement('Meh', $package_class);
    $this->assertEquals($expected, $use_statement->isTrait());
  }

  public function isTraitProvider()
  {
    return array(
        array('Bla', false),
        array('Foo\BarTraitThis', false),
        array('ClientTrait', true)
        );
  }

  /**
   * @dataProvider getAliasProvider
   */
  public function testGetAlias($namespace, $expected)
  {
    $package_class = new PackageClass('Meh', __DIR__);
    $use_statement = new UseStatement($namespace, $package_class);
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
