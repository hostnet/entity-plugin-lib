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
     * @param PackageIOInterface $package_io
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
        $trait_or_class_name = $this->package_class->getShortName();
        $interface_name      = $this->getInterfaceName($trait_or_class_name);
        $generated_namespace = $this->package_class->getGeneratedNamespaceName();

        $params = array(
            'trait_or_class_name' => $trait_or_class_name,
            'name'                => $interface_name,
            'namespace'           => $generated_namespace,
            'type_hinter'         => new TypeHinter(),
            'methods'             => $this->getMethods($this->package_class->getNamespaceName(), $trait_or_class_name)
        );

        $interface = $this->environment->render('trait_interface.php.twig', $params);
        $path      = $this->package_class->getGeneratedDirectory();
        $this->writer->writeFile($path . $interface_name . '.php', $interface);
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

    private function getInterfaceName($trait_or_class_name)
    {
        if ($this->package_class->isTrait()) {
            return $trait_or_class_name . 'Interface';
        } else {
            return $trait_or_class_name . 'TraitInterface';
        }
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
        if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
            include __DIR__ . '/../vendor/autoload.php';
        } else {
            include __DIR__ . '/../../autoload.php';
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
