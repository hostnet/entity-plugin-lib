<?php
namespace Hostnet\Component\EntityPlugin;

use Prophecy\Argument;

/**
 * More a functiononal test then a unit-test
 *
 * Tests (minimized versions of) cases that we've found in real-life
 * @covers Hostnet\Component\EntityPlugin\ReflectionGenerator
 * @author Nico Schoenmaker <nschoenmaker@hostnet.nl>
 */
class ReflectionGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerateMain()
    {
        $base_dir = __DIR__ . '/Functional/src/Entity';

        ReflectionGenerator::main('Hostnet\\FunctionalFixtures\\Entity\\BaseClass');

        $actual = file_get_contents($base_dir . '/Generated/BaseClassInterface.php');

        unlink($base_dir . '/Generated/BaseClassInterface.php');
        rmdir($base_dir . '/Generated');

        $this->assertEquals($actual, file_get_contents(__DIR__ . '/Functional/Tests/expected/BaseClassInterface.php'));
    }

    public function testGenerateWithParent()
    {
        $base_dir = __DIR__ . '/Functional/src/Entity';

        ReflectionGenerator::main('Hostnet\\FunctionalFixtures\\Entity\\ExtendedClass');

        $actual = file_get_contents($base_dir . '/Generated/ExtendedClassInterface.php');

        unlink($base_dir . '/Generated/ExtendedClassInterface.php');
        rmdir($base_dir . '/Generated');

        $this->assertEquals(
            $actual,
            file_get_contents(__DIR__ . '/Functional/Tests/expected/ExtendedClassInterface.php')
        );
    }

    public function testGenerateWithMissingParent()
    {
        $base_dir = __DIR__ . '/Functional/src/Entity';

        ReflectionGenerator::main('Hostnet\\FunctionalFixtures\\Entity\\ExtendedMissingParentClass');

        $actual = file_get_contents($base_dir . '/Generated/ExtendedMissingParentClassInterface.php');

        unlink($base_dir . '/Generated/ExtendedMissingParentClassInterface.php');
        rmdir($base_dir . '/Generated');

        $this->assertEquals(
            $actual,
            file_get_contents(__DIR__ . '/Functional/Tests/expected/ExtendedMissingParentClassInterface.php')
        );
    }

    public function testGenerate()
    {
        //include_once __DIR__ . '/EdgeCases/' . $package_class->getShortName() . '.php';
        $package_class = new PackageClass('\stdClass', __DIR__);
        $loader        = new \Twig_Loader_Filesystem(__DIR__ . '/../src/Resources/templates/');
        $environment   = new \Twig_Environment($loader);
        $package_io    = self::createMock(WriterInterface::class);

        $package_io->expects($this->once())
            ->method('writeFile')
            ->with(
                dirname(__DIR__) . '/Generated/stdClassInterface.php',
                $this->matchesRegularExpression('/interface stdClassInterface/')
            );

        $generator = new ReflectionGenerator($environment, $package_io);
        $this->assertNull($generator->generate($package_class));
    }

    public function testMain()
    {
        // functionallity is already tested, test for smoke...
        ReflectionGenerator::main('Hostnet\\FunctionalFixtures\\Entity\\BaseClass');

        $base_dir = __DIR__ . '/Functional/src/Entity';

        unlink($base_dir . '/Generated/BaseClassInterface.php');
        rmdir($base_dir . '/Generated');
    }

    public function testMainWithParent()
    {
        // functionallity is already tested, test for smoke...
        ReflectionGenerator::main(
            'Hostnet\\FunctionalFixtures\\Entity\\ExtendedClass'
        );

        $base_dir = __DIR__ . '/Functional/src/Entity';

        unlink($base_dir . '/Generated/ExtendedClassInterface.php');
        rmdir($base_dir . '/Generated');
    }
}
