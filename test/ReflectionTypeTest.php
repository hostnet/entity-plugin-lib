<?php
/**
 * @copyright 2016-present Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Component\EntityPlugin;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Hostnet\Component\EntityPlugin\ReflectionType
 */
class ReflectionTypeTest extends TestCase
{
    /**
     * @var ReflectionType
     */
    private $t1;

    /**
     * @var ReflectionType
     */
    private $t2;

    private function method(array $param = null, \Exception $param_2): void
    {
        // for testing only;
    }

    protected function setUp(): void
    {
        if (PHP_MAJOR_VERSION < 7) {
            $this->markTestSkipped('ReflectionType is available since PHP7');
        }

        $this->t1 = new ReflectionType((new \ReflectionParameter([$this, 'method'], 'param'))->getType());
        $this->t2 = new ReflectionType((new \ReflectionParameter([$this, 'method'], 'param_2'))->getType());
    }

    public function testGetName(): void
    {
        $this->assertSame('array', $this->t1->getName());
        $this->assertSame('\Exception', $this->t2->getName());
    }

    public function testAllowsNull(): void
    {
        $this->assertTrue($this->t1->allowsNull());
        $this->assertFalse($this->t2->allowsNull());
    }
}
