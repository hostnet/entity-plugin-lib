<?php
/**
 * @copyright 2015-present Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Component\EntityPlugin;

use Hostnet\Component\EntityPlugin\Fixtures\Reflection;
use Hostnet\Component\EntityPlugin\Fixtures\ReflectionReturn;
use Hostnet\Component\EntityPlugin\Fixtures\ReflectionReturnSelf;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hostnet\Component\EntityPlugin\ReflectionMethod
 */
class ReflectionMethodTest extends TestCase
{
    /**
     * @var ReflectionMethod
     */
    private $method;

    protected function setUp(): void
    {
        $this->method = new ReflectionMethod(new \ReflectionMethod(Reflection::class, 'docBlock'));
    }

    public function testGetName(): void
    {
        $this->assertEquals('docBlock', $this->method->getName());
    }

    public function testIsStatic(): void
    {
        $this->assertFalse($this->method->isStatic());
    }

    public function testIsPublic(): void
    {
        $this->assertTrue($this->method->isPublic());
    }

    public function testGetParameters(): void
    {
        $this->assertCount(3, $this->method->getParameters());
    }

    public function testGetDocComment(): void
    {
        $this->assertEquals(Reflection::getExpected(), $this->method->getDocComment());
        $method = new ReflectionMethod(new \ReflectionMethod(Reflection::class, 'extra'));
        $this->assertSame("/**\n     * I am from a trait\n     */", $method->getDocComment());
    }

    public function testGetDocCommentInvalid(): void
    {
        $method = new ReflectionMethod(new \ReflectionMethod(Reflection::class, 'invalidDocBlock'));
        $this->assertSame('/** @param ~~~\o/~~~ $param_1 */', $method->getDocComment());
    }

    public function testGetReturnType(): void
    {
        $this->assertEquals(null, $this->method->getReturnType());

        if (PHP_MAJOR_VERSION >= 7) {
            $php7_method = new ReflectionMethod(new \ReflectionMethod(ReflectionReturn::class, 'docBlock'));
            $this->assertEquals('array', $php7_method->getReturnType()->getName());
        }
    }

    public function testGetReturnTypeSelf(): void
    {
        $this->assertEquals(null, $this->method->getReturnType());

        if (PHP_MAJOR_VERSION >= 7) {
            $php7_method = new ReflectionMethod(new \ReflectionMethod(ReflectionReturnSelf::class, 'docBlock'));
            $this->assertEquals(null, $php7_method->getReturnType());
        }
    }
}
