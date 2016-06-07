<?php
namespace Hostnet\Component\EntityPlugin;

use Hostnet\Component\EntityPlugin\WriterInterface;

/**
 * @covers Hostnet\Component\EntityPlugin\EmptyGenerator
 */
class EmptyGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the empty Generator.
     */
    public function testEmptyGenerator()
    {
        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../src/Resources/templates/');

        $environment = new \Twig_Environment($loader);
        $writer      = $this->prophesize(WriterInterface::class);

        $interface = <<<EOI
<?php
namespace \A\Namespace;

/**
 * Implement this interface in UnitTest!
 * This is a combined interface that will automatically extend to contain the required functions.
 */
interface UnitTestInterface
{
}

EOI;

        $writer->writeFile('/tmp/unit-test/UnitTestInterface.php', $interface)->shouldBeCalled();

        $empty_generator = new EmptyGenerator(
            $environment,
            $writer->reveal()
        );

        $package_class = $this->prophesize(PackageClass::class);
        $package_class->getShortName()->willReturn('UnitTest');
        $package_class->getGeneratedNamespaceName()->willReturn('\A\Namespace');
        $package_class->getGeneratedDirectory()->willReturn('/tmp/unit-test/');

        $empty_generator->generate($package_class->reveal());
    }
}
