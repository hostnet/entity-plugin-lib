<?php
namespace Hostnet\Component\EntityPlugin;

/**
 * @covers Hostnet\Component\EntityPlugin\PackageContent
 */
class PackageContentTest extends \PHPUnit_Framework_TestCase
{
    private static $map = [
        'Foo\Bar\Baz' => 'Baz.php',
        'Foo\Bar\BazInterface' => 'BazInterface.php',
        'Foo\Bar\BazException' => 'BazException.php',
        'Foo\Bar\BlahTrait' => 'BlahTrait.php',
        'Foo\Bar\BazWhenBlahTrait' => '/BazWhenBlahTrait.php',
        'Foo\Bar\Generated\Baz' => 'Generated/Baz.php',
        'Foo\Baz\Bar' => 'Bar.php'
    ];

    private $empty_content;

    private $content;

    protected function setUp()
    {
        $this->empty_content = new PackageContent([], '\\Bar\\');
        $this->content = new PackageContent(self::$map, '\\Bar\\');
    }

    public function testGetClasses()
    {
        $this->assertEquals([], $this->empty_content->getClasses());
        $this->assertEquals(
            [new PackageClass('Foo\Bar\Baz', 'Baz.php')],
            $this->content->getClasses()
        );
    }

    public function testGetClassOrTrait()
    {
        $this->assertNull($this->empty_content->getClassOrTrait('Baz'));
        $this->assertEquals(
            new PackageClass('Foo\Bar\Baz', 'Baz.php'),
            $this->content->getClassOrTrait('Baz')
        );
        $this->assertEquals(
            new PackageClass('Foo\Bar\BlahTrait', 'BlahTrait.php'),
            $this->content->getClassOrTrait('Blah')
        );
        $this->assertNull($this->content->getClassOrTrait('Bar'));
    }

    public function testGetOptionalTraits()
    {
        $this->assertEquals([], $this->empty_content->getOptionalTraits('Baz'));
        $this->assertEquals(
            [new OptionalPackageTrait('Foo\Bar\BazWhenBlahTrait', '/BazWhenBlahTrait.php', 'Blah')],
            $this->content->getOptionalTraits('Baz')
        );
        $this->assertEquals([], $this->content->getOptionalTraits('Blah'));
    }

    public function testGetTraits()
    {
        $this->assertEquals([], $this->empty_content->getTraits());
        $this->assertEquals(
            [new PackageClass('Foo\Bar\BlahTrait', 'BlahTrait.php')],
            $this->content->getTraits()
        );
    }

    public function testHasClass()
    {
        $this->assertFalse($this->empty_content->hasClass('Bar'));
        $this->assertFalse($this->content->hasClass('Bar'));
        $this->assertTrue($this->content->hasClass('Baz'));
    }
}
