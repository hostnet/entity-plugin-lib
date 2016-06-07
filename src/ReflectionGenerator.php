<?php
namespace Hostnet\Component\EntityPlugin;

/**
 * A simple, light-weight generator that can be used runtime during development
 * It does not know about the composer structure, since thats expensive to build
 *
 * @author Nico Schoenmaker <nschoenmaker@hostnet.nl>
 */
class ReflectionGenerator
{
    private $environment;

    private $writer;

    /**
     * @param \Twig_Environment $environment
     * @param WriterInterface $writer
     */
    public function __construct(
        \Twig_Environment $environment,
        WriterInterface $writer
    ) {
        $this->environment = $environment;
        $this->writer      = $writer;
    }

    /**
     * Generates the interface
     *
     * @param PackageClass $package_class
     * @param PackageClass $parent
     * @throws \Twig_Error
     */
    public function generate(PackageClass $package_class, PackageClass $parent = null)
    {
        $class_name          = $package_class->getShortName();
        $generated_namespace = $package_class->getGeneratedNamespaceName();

        $params = [
            'class_name' => $class_name,
            'namespace' => $generated_namespace,
            'type_hinter' => new TypeHinter(),
            'methods' => $this->getMethods($package_class),
            'parent' => $parent ? $parent->getShortName() : null
        ];

        $interface = $this->environment->render('interface.php.twig', $params);
        $path      = $package_class->getGeneratedDirectory();
        $this->writer->writeFile($path . $class_name . 'Interface.php', $interface);
    }

    /**
     * Which methods do we have to generate?
     *
     * @return \ReflectionMethod[]
     */
    protected function getMethods(PackageClass $package_class)
    {
        $class   = new \ReflectionClass($package_class->getName());
        $methods = $class->getMethods();

        foreach ($methods as $key => $method) {
            if ($method->name === '__construct') {
                // The interface should not contain the constructor
                unset($methods[$key]);
                continue;
            }
            $methods[$key] = new ReflectionMethod($method);
        }

        return $methods;
    }

    private static function getParentClass(\ReflectionClass $base_class)
    {
        if (false === ($parent_reflection = $base_class->getParentClass())
            || dirname($parent_reflection->getFileName()) !== dirname($base_class->getFileName())
        ) {
            return null;
        }

        return new PackageClass($parent_reflection->getName(), $parent_reflection->getFileName());
    }

    /**
     * Generation entry point.
     *
     * @param string $class Fully Qualified class name to generate interface for
     */
    public static function main($class)
    {
        // setup all the dependencies
        $reflection           = new \ReflectionClass($class);
        $package_class        = new PackageClass($class, $reflection->getFileName());
        $parent_package_class = self::getParentClass($reflection);

        $loader      = new \Twig_Loader_Filesystem(__DIR__ . '/Resources/templates/');
        $environment = new \Twig_Environment($loader);
        $writer      = new Writer();

        // generate the files
        $generator = new ReflectionGenerator($environment, $writer);
        $generator->generate($package_class, $parent_package_class);
    }
}
