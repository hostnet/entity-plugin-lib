<?php
namespace Hostnet\Component\EntityPlugin;

use PHPUnit\Framework\TestCase;

/**
 * @covers Hostnet\Component\EntityPlugin\PackageContent
 */
class PackageContentTest extends TestCase
{
    private static $map = [
        'Foo\\Bar\\Baz' => 'Baz.php',
        'Foo\\Bar\\BazInterface' => 'BazInterface.php',
        'Foo\\Bar\\BazException' => 'BazException.php',
        'Foo\\Bar\\BlahTrait' => 'BlahTrait.php',
        'Foo\\Bar\\BazWhenBlahTrait' => '/BazWhenBlahTrait.php',
        'Foo\\Bar\\Generated\\Baz' => 'Generated/Baz.php',
        'Foo\\Baz\\Bar' => 'Bar.php',
        'Hostnet\\Drink\\Entity\\Milk' => '/Milk.php',
        'Hostnet\\Animal\\Entity\\Cow' => '/Cow.php',
        'Hostnet\\AnimalDrink\\Entity\\MilkWhenCowTrait' => '/MilkWhenCowTrait.php',
        'Hostnet\\AnimalDrink\\Entity\\CowWhenMilkTrait' => '/CowWhenMilkTrait.php',
    ];

    private $empty_content;

    private $content;

    protected function setUp()
    {
        $this->empty_content = new PackageContent([], '\\Bar\\');
        $this->content       = new PackageContent(self::$map, '\\Bar\\');
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

        // Optional trait for class included in own package
        $this->assertEquals(
            [new OptionalPackageTrait('Foo\Bar\BazWhenBlahTrait', '/BazWhenBlahTrait.php', 'Blah')],
            $this->content->getOptionalTraits('Baz')
        );

        $animaldrink = new PackageContent(self::$map, '\\AnimalDrink\\');

        // Optional trait for class included in other package
        $this->assertEquals(
            [
                new OptionalPackageTrait(
                    'Hostnet\\AnimalDrink\\Entity\\CowWhenMilkTrait',
                    '/CowWhenMilkTrait.php',
                    'Milk'
                )
            ],
            $animaldrink->getOptionalTraits('Cow')
        );
        $this->assertEquals(
            [
                new OptionalPackageTrait(
                    'Hostnet\\AnimalDrink\\Entity\\MilkWhenCowTrait',
                    '/MilkWhenCowTrait.php',
                    'Cow'
                )
            ],
            $animaldrink->getOptionalTraits('Milk')
        );
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
