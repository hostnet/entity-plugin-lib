<?php
namespace Hostnet\Component\EntityPlugin;

use Hostnet\Component\EntityPlugin\Fixtures\DefaultValueParams;
use Hostnet\Component\EntityPlugin\Fixtures\ExtendedReturnType;
use Hostnet\Component\EntityPlugin\Fixtures\NullableParams;
use Hostnet\Component\EntityPlugin\Fixtures\ReturnType;
use Hostnet\Component\EntityPlugin\Fixtures\ScalarParams;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * More a functional test then a unit-test
 *
 * Tests (minimized versions of) cases that we've found in real-life
 * @covers \Hostnet\Component\EntityPlugin\ReflectionGenerator
 */
class ReflectionGeneratorTest extends TestCase
{
    use ProphecyTrait;

    /**
     * The ReflectionGenerator under test
     *
     * @var ReflectionGenerator
     */
    private $reflection_generator;

    /**
     * The Twig enviroment used to load the twig templates from.
     *
     * @var Environment
     */
    private $environment;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $loader            = new FilesystemLoader(__DIR__ . '/../src/Resources/templates/');
        $this->environment = new Environment($loader);
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

    public function testGenerateWithScalarParams()
    {
        if (PHP_MAJOR_VERSION < 7) {
            $this->markTestSkipped('Return types are not available in PHP <7');
        }

        $package_class = new PackageClass(ScalarParams::class, __DIR__ . '/Fixtures/ScalarParams.php');
        $this->reflection_generator->generate($package_class);

        $expected = file_get_contents(__DIR__ . '/Fixtures/ScalarParamsInterface.php');
        $actual   = file_get_contents(__DIR__ . '/Fixtures/Generated/ScalarParamsInterface.php');

        unlink(__DIR__ . '/Fixtures/Generated/ScalarParamsInterface.php');
        rmdir(__DIR__ . '/Fixtures/Generated');

        $this->assertSame($expected, $actual);
    }

    public function testGenerateWithNullableParams()
    {
        if (PHP_VERSION_ID < 70100) {
            $this->markTestSkipped('Return types are not available in PHP <7');
        }

        $package_class = new PackageClass(NullableParams::class, __DIR__ . '/Fixtures/NullableParams.php');
        $this->reflection_generator->generate($package_class);

        $expected = file_get_contents(__DIR__ . '/Fixtures/NullableParamsInterface.php');
        $actual   = file_get_contents(__DIR__ . '/Fixtures/Generated/NullableParamsInterface.php');

        unlink(__DIR__ . '/Fixtures/Generated/NullableParamsInterface.php');
        rmdir(__DIR__ . '/Fixtures/Generated');

        $this->assertSame($expected, $actual);
    }

    public function testGenerateWithDefaultValueParams()
    {
        if (PHP_MAJOR_VERSION < 7) {
            $this->markTestSkipped('Scalar argument types are not available in PHP <7');
        }

        $package_class = new PackageClass(DefaultValueParams::class, __DIR__ . '/Fixtures/DefaultValueParams.php');
        $this->reflection_generator->generate($package_class);

        $expected = file_get_contents(__DIR__ . '/Fixtures/DefaultValueParamsInterface.php');
        $actual   = file_get_contents(__DIR__ . '/Fixtures/Generated/DefaultValueParamsInterface.php');

        unlink(__DIR__ . '/Fixtures/Generated/DefaultValueParamsInterface.php');
        rmdir(__DIR__ . '/Fixtures/Generated');

        $this->assertSame($expected, $actual);
    }

    public function testGenerateWithReturnType()
    {
        if (PHP_MAJOR_VERSION < 7) {
            $this->markTestSkipped('Return types are not available in PHP <7');
        }

        $empty_generator = new EmptyGenerator($this->environment, new Filesystem());
        $package_class   = new PackageClass(ReturnType::class, __DIR__ . '/Fixtures/ReturnType.php');

        $empty_generator->generate($package_class);
        $this->reflection_generator->generate($package_class);

        $expected = file_get_contents(__DIR__ . '/Fixtures/ReturnTypeInterface.php');
        $actual   = file_get_contents(__DIR__ . '/Fixtures/Generated/ReturnTypeInterface.php');

        unlink(__DIR__ . '/Fixtures/Generated/ReturnTypeInterface.php');
        rmdir(__DIR__ . '/Fixtures/Generated');

        $this->assertSame($expected, $actual);
    }

    public function testGenerateWithExtendedReturnType()
    {
        if (PHP_VERSION_ID < 70100) {
            $this->markTestSkipped('Return types are not available in PHP <7.1');
        }

        $empty_generator = new EmptyGenerator($this->environment, new Filesystem());
        $package_class   = new PackageClass(ExtendedReturnType::class, __DIR__ . '/Fixtures/ExtendedReturnType.php');
        $empty_generator->generate($package_class);

        $package_class = new PackageClass(ExtendedReturnType::class, __DIR__ . '/Fixtures/ExtendedReturnType.php');
        $this->reflection_generator->generate($package_class);


        $expected = file_get_contents(__DIR__ . '/Fixtures/ExtendedReturnTypeInterface.php');
        $actual   = file_get_contents(__DIR__ . '/Fixtures/Generated/ExtendedReturnTypeInterface.php');

        unlink(__DIR__ . '/Fixtures/Generated/ExtendedReturnTypeInterface.php');
        rmdir(__DIR__ . '/Fixtures/Generated');

        $this->assertSame($expected, $actual);
    }

    public function testGenerate()
    {
        $package_class = new PackageClass('\stdClass', __DIR__);
        $package_io    = self::createMock(Filesystem::class);

        $package_io
            ->expects($this->once())
            ->method('dumpFile')
            ->with(
                dirname(__DIR__) . '/Generated/stdClassInterface.php',
                $this->matchesRegularExpression('/interface stdClassInterface/')
            );

        $generator = new ReflectionGenerator($this->environment, $package_io);
        $this->assertNull($generator->generate($package_class));
    }

    public function testGenerateReflectionErrorOnClass()
    {
        $filesystem           = $this->prophesize(Filesystem::class);
        $reflection_generator = new ReflectionGenerator($this->environment, $filesystem->reveal());

        $package_class = new PackageClass('A\Non\Exsisting\Class', sys_get_temp_dir() . '/file.php');
        $reflection_generator->generate($package_class);

        $filesystem->dumpFile(Argument::any())->shouldNotBeCalled();
    }
}
