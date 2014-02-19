<?php
namespace Hostnet\Component\EntityPlugin;

class UseStatementTest extends \PHPUnit_Framework_TestCase
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
            array(
                'Bla',
                false
            ),
            array(
                'Foo\BarTraitThis',
                false
            ),
            array(
                'ClientTrait',
                true
            )
        );
    }

    public function testGetNamespace()
    {
        $package_class = new PackageClass('Blah', __DIR__);
        $namespace = 'Hostnet\Foo\Entity\Bar';
        $use_statement = new UseStatement($namespace, $package_class);
        $this->assertEquals($namespace, $use_statement->getNamespace());
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
            array(
                'Hihihi\Hahaha\Hohoho',
                'HihihiHahahaHohoho'
            ),
            array(
                'Hostnet\Client\Entity\Client',
                'HostnetClientEntityClient'
            )
        );
    }
}
