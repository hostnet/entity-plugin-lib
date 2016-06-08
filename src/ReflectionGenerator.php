<?php
namespace Hostnet\Component\EntityPlugin;

use Symfony\Component\Filesystem\Filesystem;

/**
 * A simple, light-weight generator that can be used runtime during development
 * It does not know about the composer structure, since thats expensive to build
 *
 * @author Nico Schoenmaker <nschoenmaker@hostnet.nl>
 */
class ReflectionGenerator
{
    private $environment;

    private $filesystem;

    /**
     * @param \Twig_Environment $environment
     * @param Filesystem $filesystem
     */
    public function __construct(
        \Twig_Environment $environment,
        Filesystem $filesystem
    ) {
        $this->environment = $environment;
        $this->filesystem  = $filesystem;
    }

    /**
     * Generates the interface
     *
     * @param PackageClass $package_class
     * @throws \Twig_Error
     */
    public function generate(PackageClass $package_class)
    {
        $parent              = $this->getParentClass($package_class);
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
        $this->filesystem->dumpFile($path . $class_name . 'Interface.php', $interface);
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

    /**
     * Get the Parent of the given base class, if any.
     *
     * @param PackageClass $package_class the base for which the parent needs to be extracted.
     * @return NULL|\Hostnet\Component\EntityPlugin\PackageClass the parent class if any, otherwise null is returned.
     */
    private function getParentClass(PackageClass $package_class)
    {
        try {
            $base_class = new \ReflectionClass($package_class->getName());
        } catch (\ReflectionException $e) {
            return null;
        }
        if (false === ($parent_reflection = $base_class->getParentClass())
            || dirname($parent_reflection->getFileName()) !== dirname($base_class->getFileName())
        ) {
            return null;
        }
        return new PackageClass($parent_reflection->getName(), $parent_reflection->getFileName());
    }
}
