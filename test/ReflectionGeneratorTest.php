<?php
namespace Hostnet\Component\EntityPlugin;

use Symfony\Component\Filesystem\Filesystem;

/**
 * More a functiononal test then a unit-test
 *
 * Tests (minimized versions of) cases that we've found in real-life
 * @covers Hostnet\Component\EntityPlugin\ReflectionGenerator
 * @author Nico Schoenmaker <nschoenmaker@hostnet.nl>
 */
class ReflectionGeneratorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The ReflectionGenerator under test
     *
     * @var ReflectionGenerator
     */
    private $reflection_generator;

    /**
     * The Twig enviroment used to load the twig templates from.
     *
     * @var \Twig_Environment
     */
    private $environment;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $loader            = new \Twig_Loader_Filesystem(__DIR__ . '/../src/Resources/templates/');
        $this->environment = new \Twig_Environment($loader);
        $filesystem        = new Filesystem();

        // generate the files
        $this->reflection_generator = new ReflectionGenerator($this->environment, $filesystem);
    }

    public function testGenerateMain()
    {
        $base_dir = __DIR__ . '/Functional/src/Entity';

        $reflection    = new \ReflectionClass('Hostnet\\FunctionalFixtures\\Entity\\BaseClass');
        $package_class = new PackageClass('Hostnet\\FunctionalFixtures\\Entity\\BaseClass', $reflection->getFileName());
        $this->reflection_generator->generate($package_class);

        $actual = file_get_contents($base_dir . '/Generated/BaseClassInterface.php');

        unlink($base_dir . '/Generated/BaseClassInterface.php');
        rmdir($base_dir . '/Generated');

        $this->assertEquals($actual, file_get_contents(__DIR__ . '/Functional/Tests/expected/BaseClassInterface.php'));
    }

    public function testGenerateWithParent()
    {
        $base_dir = __DIR__ . '/Functional/src/Entity';

        $reflection    = new \ReflectionClass('Hostnet\\FunctionalFixtures\\Entity\\ExtendedClass');
        $package_class = new PackageClass(
            'Hostnet\\FunctionalFixtures\\Entity\\ExtendedClass',
            $reflection->getFileName()
        );
        $this->reflection_generator->generate($package_class);

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

        $reflection    = new \ReflectionClass('Hostnet\\FunctionalFixtures\\Entity\\ExtendedMissingParentClass');
        $package_class = new PackageClass(
            'Hostnet\\FunctionalFixtures\\Entity\\ExtendedMissingParentClass',
            $reflection->getFileName()
        );
        $this->reflection_generator->generate($package_class);

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
        $package_io    = self::createMock(Filesystem::class);

        $package_io->expects($this->once())
            ->method('dumpFile')
            ->with(
                dirname(__DIR__) . '/Generated/stdClassInterface.php',
                $this->matchesRegularExpression('/interface stdClassInterface/')
            );

        $generator = new ReflectionGenerator($this->environment, $package_io);
        $this->assertNull($generator->generate($package_class));
    }

    /**
     * @expectedException \ReflectionException
     */
    public function testGenerateReflectionErrorOnClass()
    {
        $filesystem           = $this->prophesize(Filesystem::class);
        $reflection_generator = new ReflectionGenerator($this->environment, $filesystem->reveal());

        $package_class = new PackageClass('A\Non\Exsisting\Class', sys_get_temp_dir() . '/file.php');
        $reflection_generator->generate($package_class);

    }
}
