<?php
/**
 * @copyright 2016-present Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Component\EntityPlugin;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputArgument;

/**
 * @covers \Hostnet\Component\EntityPlugin\ReflectionParameter
 */
class ReflectionParameterTest extends TestCase
{
    private const FOO = 'BAR';

    private function method(array $param = null, $param_2, \Exception $param_3, $param_4 = \DateTime::ATOM): void
    {
        // for testing only;
    }

    public function testGetType(): void
    {
        $p1 = new ReflectionParameter(new \ReflectionParameter([$this, 'method'], 'param'));
        $p2 = new ReflectionParameter(new \ReflectionParameter([$this, 'method'], 'param_2'));
        $p3 = new ReflectionParameter(new \ReflectionParameter([$this, 'method'], 'param_3'));

        $this->assertEquals('array', $p1->getType()->getName());
        $this->assertNull($p2->getType());
        $this->assertEquals('\\' . \Exception::class, $p3->getType()->getName());
    }

    public function testHasType(): void
    {
        $php_parameter = new \ReflectionParameter([$this, 'method'], 'param');
        $our_parameter = new ReflectionParameter($php_parameter);

        $this->assertTrue($our_parameter->hasType());
    }

    public function testWrappedMethods(): void
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
        $this->assertSame($php_parameter->isDefaultValueConstant(), $our_parameter->isDefaultValueConstant());
        $this->assertSame($php_parameter->getDefaultValueConstantName(), $our_parameter->getDefaultValueConstantName());

        $php_parameter = new \ReflectionParameter([$this, 'method'], 'param_4');
        $our_parameter = new ReflectionParameter($php_parameter);
        $this->assertSame($php_parameter->isDefaultValueConstant(), $our_parameter->isDefaultValueConstant());
        $this->assertSame($php_parameter->getDefaultValueConstantName(), $our_parameter->getDefaultValueConstantName());
    }

    private function sampleMethod(
        $bool,
        $true_bool = true,
        $false_bool = false,
        $null_bool = null,
        $const_datetime = \DateTime::ATOM,
        $const_self = self::FOO,
        $const_namespaced = InputArgument::REQUIRED,
        $array = [],
        $string_as_int = 1,
        $string = 'foo'
    ): void {
        // for testing only;
    }

    /**
     * @dataProvider getPhpSafeDefaultValueProvider
     */
    public function testGetPhpSafeDefaultValue($name, $expected): void
    {
        $php_parameter = new \ReflectionParameter([$this, 'sampleMethod'], $name);
        $our_parameter = new ReflectionParameter($php_parameter);
        $this->assertSame($expected, $our_parameter->getPhpSafeDefaultValue());
    }

    public function getPhpSafeDefaultValueProvider(): iterable
    {
        return [
            ['true_bool', 'true'],
            ['false_bool', 'false'],
            ['null_bool', 'null'],
            ['const_datetime', '\DateTime::ATOM'],
            ['const_self', 'self::FOO'],
            ['const_namespaced', '\Symfony\Component\Console\Input\InputArgument::REQUIRED'],
            ['array', '[]'],
            ['string_as_int', '1'],
            ['string', '\'foo\''],
        ];
    }

    public function testGetPhpSafeDefaultValueException(): void
    {
        $php_parameter = new \ReflectionParameter([$this, 'sampleMethod'], 'bool');
        $our_parameter = new ReflectionParameter($php_parameter);

        $this->expectException(\ReflectionException::class);
        $our_parameter->getPhpSafeDefaultValue();
    }
}
