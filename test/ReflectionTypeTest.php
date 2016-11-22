<?php
namespace Hostnet\Component\EntityPlugin;

/**
 * @covers \Hostnet\Component\EntityPlugin\ReflectionType
 */
class ReflectionTypeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ReflectionType
     */
    private $t1;

    /**
     * @var ReflectionType
     */
    private $t2;

    private function method(array $param = null, \Exception $param_2)
    {
        // for testing only;
    }

    protected function setUp()
    {
        if (PHP_MAJOR_VERSION < 7) {
            $this->markTestSkipped('ReflectionType is available since PHP7');
        }

        $this->t1 = new ReflectionType((new \ReflectionParameter([$this, 'method'], 'param'))->getType());
        $this->t2 = new ReflectionType((new \ReflectionParameter([$this, 'method'], 'param_2'))->getType());
    }

    public function testGetName()
    {
        $this->assertSame('array', $this->t1->getName());
        $this->assertSame('\Exception', $this->t2->getName());
    }

    public function testAllowsNull()
    {
        $this->assertTrue($this->t1->allowsNull());
        $this->assertFalse($this->t2->allowsNull());
    }
}
