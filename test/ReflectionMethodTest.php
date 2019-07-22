<?php
namespace Hostnet\Component\EntityPlugin;

use Hostnet\Component\EntityPlugin\Fixtures\Reflection;
use Hostnet\Component\EntityPlugin\Fixtures\ReflectionReturn;
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

    public function testGetName()
    {
        $this->assertEquals('docBlock', $this->method->getName());
    }

    public function testIsStatic()
    {
        $this->assertFalse($this->method->isStatic());
    }

    public function testIsPublic()
    {
        $this->assertTrue($this->method->isPublic());
    }

    public function testGetParameters()
    {
        $this->assertCount(3, $this->method->getParameters());
    }

    public function testGetDocComment()
    {
        $this->assertEquals(Reflection::getExpected(), $this->method->getDocComment());
        $method = new ReflectionMethod(new \ReflectionMethod(Reflection::class, 'extra'));
        $this->assertSame("/**\n     * I am from a trait\n     */", $method->getDocComment());
    }

    public function testGetDocCommentInvalid()
    {
        $method = new ReflectionMethod(new \ReflectionMethod(Reflection::class, 'invalidDocBlock'));
        $this->assertSame('/** @param ~~~\o/~~~ $param_1 */', $method->getDocComment());
    }

    public function testGetReturnType()
    {
        $this->assertEquals(null, $this->method->getReturnType());

        if (PHP_MAJOR_VERSION >= 7) {
            $php7_method = new ReflectionMethod(new \ReflectionMethod(ReflectionReturn::class, 'docBlock'));
            $this->assertEquals('array', $php7_method->getReturnType()->getName());
        }
    }
}
