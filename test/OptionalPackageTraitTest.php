<?php
/**
 * @copyright 2015-present Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Component\EntityPlugin;

use phpunit\framework\TestCase;

/**
 * More of a functional-like test to check the outputted html.
 * @covers \Hostnet\Component\EntityPlugin\OptionalPackageTrait
 * @covers \Hostnet\Component\EntityPlugin\OptionalPackageTrait
 */
class OptionalPackageTraitTest extends TestCase
{
    public function testConstruct(): void
    {
        $trait = new OptionalPackageTrait('Foo\Bar\A', __FILE__, 'B');
        $this->assertSame('Foo\Bar\A', $trait->getName());
        $this->assertSame(__DIR__ . '/Generated/', $trait->getGeneratedDirectory());
        $this->assertSame('B', $trait->getRequirement());
        $this->assertSame('FooBarBecauseB', $trait->getAlias());
    }
}
