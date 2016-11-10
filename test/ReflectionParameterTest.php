<?php
namespace Hostnet\Component\EntityPlugin;

/**
 * @covers \Hostnet\Component\EntityPlugin\ReflectionParameter
 */
class ReflectionParameterTest extends \PHPUnit_Framework_TestCase
{
    private function method(array $param = null, $param_2, \Exception $param_3)
    {
        // for testing only;
    }

    public function testGetType()
    {
        $p1 = new ReflectionParameter(new \ReflectionParameter([$this, 'method'], 'param'));
        $p2 = new ReflectionParameter(new \ReflectionParameter([$this, 'method'], 'param_2'));
        $p3 = new ReflectionParameter(new \ReflectionParameter([$this, 'method'], 'param_3'));

        $this->assertEquals('array', $p1->getType()->getName());
        $this->assertNull($p2->getType());
        $this->assertEquals('\\' . \Exception::class, $p3->getType()->getName());
    }

    public function testHasType()
    {
        $php_parameter = new \ReflectionParameter([$this, 'method'], 'param');
        $our_parameter = new ReflectionParameter($php_parameter);

        $this->assertTrue($our_parameter->hasType());
    }

    public function testWrappedMethods()
    {
        $php_parameter = new \ReflectionParameter([$this, 'method'], 'param');
        $our_parameter = new ReflectionParameter($php_parameter);

        $this->assertSame($php_parameter->getName(), $our_parameter->getName());
        $this->assertSame($php_parameter->allowsNull(), $our_parameter->allowsNull());
        $this->assertSame($php_parameter->isOptional(), $our_parameter->isOptional());
        $this->assertSame($php_parameter->isDefaultValueAvailable(), $our_parameter->isDefaultValueAvailable());
        $this->assertSame($php_parameter->isVariadic(), $our_parameter->isVariadic());
        $this->assertSame($php_parameter->isPassedByReference(), $our_parameter->isPassedByReference());
        $this->assertSame($php_parameter->getDefaultValue(), $our_parameter->getDefaultValue());
    }
}
