<?php
namespace Hostnet\Component\EntityPlugin;

class TypeHinterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider getTypeHintProvider
     */
    public function testGetTypeHint(\ReflectionParameter $parameter, $expected)
    {
        $hinter = new TypeHinter();
        $this->assertEquals($expected, $hinter->getTypeHint($parameter));
    }

    public function getTypeHintProvider()
    {
        $array_param = $this->getMockBuilder('ReflectionParameter')
            ->disableOriginalConstructor()
            ->getMock();
        $array_param->expects($this->once())
            ->method('isArray')
            ->will($this->returnValue(true));

        $typed_param = $this->getMockBuilder('ReflectionParameter')
            ->disableOriginalConstructor()
            ->getMock();
        $typed_param->expects($this->once())
            ->method('isArray')
            ->will($this->returnValue(false));
        $typed_param->expects($this->any())
            ->method('__toString')
            ->will($this->returnValue('Parameter #0 [ <optional> DateTime $date ]'));

        $typed_required_param = $this->getMockBuilder('ReflectionParameter')
            ->disableOriginalConstructor()
            ->getMock();
        $typed_required_param->expects($this->once())
            ->method('isArray')
            ->will($this->returnValue(false));
        $typed_required_param->expects($this->any())
            ->method('__toString')
            ->will($this->returnValue('Parameter #0 [ <required> DateTime $date ]'));

        $namespaced_param = $this->getMockBuilder('ReflectionParameter')
            ->disableOriginalConstructor()
            ->getMock();
        $namespaced_param->expects($this->once())
            ->method('isArray')
            ->will($this->returnValue(false));
        $namespaced_param->expects($this->any())
            ->method('__toString')
            ->will($this->returnValue(
                'Parameter #0 [ <required> Hostnet\Component\EntityPlugin\ReflectionGenerator $generator ]'
            ));
        return array(
            array(
                $array_param,
                'array '
            ),
            array(
                $typed_param,
                '\DateTime '
            ),
            array(
                $typed_required_param,
                '\DateTime '
            ),
            array(
                $namespaced_param,
                '\Hostnet\Component\EntityPlugin\ReflectionGenerator '
            )
        );
    }
}
