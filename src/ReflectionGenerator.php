<?php
namespace Hostnet\Component\EntityPlugin;

use Composer\IO\IOInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * A simple, light-weight generator that can be used runtime during development
 * It does not know about the composer structure, since thats expensive to build
 *
 * @author Nico Schoenmaker <nschoenmaker@hostnet.nl>
 */
class ReflectionGenerator
{
    private $environment;

    private $package_io;

    private $package_class;

    /**dirname
     * @param \Twig_Environment $environment
     * @param PackageContentInterface $package_io
     * @param PackageClass $package_class
     */
    public function __construct(
        \Twig_Environment $environment,
        WriterInterface $writer,
        PackageClass $package_class
    ) {
        $this->environment   = $environment;
        $this->writer        = $writer;
        $this->package_class = $package_class;
    }

    /**
     * Generates the interface
     *
     * @throws \RuntimeException
     */
    public function generate()
    {
        $class_name          = $this->package_class->getShortName();
        $generated_namespace = $this->package_class->getGeneratedNamespaceName();

        $params = [
            'class_name'  => $class_name,
            'namespace'   => $generated_namespace,
            'type_hinter' => new TypeHinter(),
            'methods'     => $this->getMethods($this->package_class->getNamespaceName(), $class_name)
        ];

        $interface = $this->environment->render('interface.php.twig', $params);
        $path      = $this->package_class->getGeneratedDirectory();
        $this->writer->writeFile($path . $class_name . 'Interface.php', $interface);
    }

    /**
     * Which methods do we have to generate?
     *
     * @return \ReflectionMethod[]
     */
    protected function getMethods()
    {
        $class   = new \ReflectionClass($this->package_class->getName());
        $methods = $class->getMethods();

        foreach ($methods as $key => $method) {
            if ($method->name === '__construct') {
                // The interface should not contain the constructor
                unset($methods[$key]);
            }
        }

        return $methods;
    }

    /**
     *
     * @param string $class Fully Qualified class name to generate interface for
     * @return string
     */
    public static function generateInIsolation($class)
    {
        $php       = '/usr/bin/env php -r';
        $namespace = 'namespace Hostnet\\Component\\EntityPlugin;';
        $require   = 'require \'' . __FILE__ . '\';';
        $main      = 'ReflectionGenerator::main(\'' . $class . '\');';
        echo `$php "$namespace $require $main"`;
    }

    /**
     * Generated in process isolation entry point.
     *
     * @param string $class Fully Qualified class name to generate interface for
     */
    public static function main($class)
    {
        // enable autoloading
        // @codeCoverageIgnoreStart
        if (file_exists(getcwd() . '/vendor/autoload.php')) {
            // If symlinked this is enables testing using ./composer.phar dump-autoload
            // in the project root directory.
            include getcwd() . '/vendor/autoload.php';
        } elseif (file_exists(__DIR__ . '/../../../autoload.php')) {
            // We are loaded in a parent project and inside the vendor directory.
            // Test this before a standalone checkout to not accidentially pick
            // the autoload created by running ./composer.phar dump-autmoload in
            // or install in the vendor directory for this package.
            include __DIR__ . '/../../../autoload.php';
        } else {
            // Stand alone checkout
            include __DIR__ . '/../vendor/autoload.php';
        }
        // @codeCoverageIgnoreEnd

        // setup all the dependencies
        $reflection    = new \ReflectionClass($class);
        $path          = $reflection->getFileName();
        $package_class = new PackageClass($class, $path);
        $loader        = new \Twig_Loader_Filesystem(__DIR__ . '/Resources/templates/');
        $environment   = new \Twig_Environment($loader);
        $writer        = new Writer();

        // generate the files
        $generator = new ReflectionGenerator($environment, $writer, $package_class);
        $generator->generate();
    }
}
