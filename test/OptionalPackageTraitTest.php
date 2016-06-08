<?php
namespace Hostnet\Component\EntityPlugin;

use phpunit\framework\TestCase;

/**
 * More of a functional-like test to check the outputted html.
 * @author Nico Schoenmaker <nschoenmaker@hostnet.nl>
 * @covers Hostnet\Component\EntityPlugin\OptionalPackageTrait
 */
class OptionalPackageTraitTest extends TestCase
{
    public function testConstruct()
    {
        $trait = new OptionalPackageTrait('Foo\Bar\A', __FILE__, 'B');
        $this->assertSame('Foo\Bar\A', $trait->getName());
        $this->assertSame(__DIR__ . '/Generated/', $trait->getGeneratedDirectory());
        $this->assertSame('B', $trait->getRequirement());
        $this->assertSame('FooBarBecauseB', $trait->getAlias());
    }
}
