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

    /**
     * @var ReflectionType
     */
    private $t3;

    private function method(array $param = null, \Exception $param_2): void
    {
        // for testing only;
    }

    private function php8method(string|int $union_param): string|int
    {
        return $union_param;
    }

    protected function setUp(): void
    {
        if (PHP_MAJOR_VERSION < 7) {
            $this->markTestSkipped('ReflectionType is available since PHP7');
        }

        $this->t1 = new ReflectionType((new \ReflectionParameter([$this, 'method'], 'param'))->getType());
        $this->t2 = new ReflectionType((new \ReflectionParameter([$this, 'method'], 'param_2'))->getType());
        $this->t3 = new ReflectionType((new \ReflectionParameter([$this, 'php8method'], 'union_param'))->getType());
    }

    public function testGetName(): void
    {
        $this->assertSame('array', $this->t1->getName());
        $this->assertSame('\Exception', $this->t2->getName());
        $this->assertSame('string|int', $this->t3->getName());
    }

    public function testAllowsNull(): void
    {
        $this->assertTrue($this->t1->allowsNull());
        $this->assertFalse($this->t2->allowsNull());
        $this->assertFalse($this->t3->allowsNull());
    }
}
