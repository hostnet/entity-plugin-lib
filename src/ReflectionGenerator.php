<?php
namespace Hostnet\Component\EntityPlugin;

use Composer\IO\IOInterface;

/**
 * A simple, light-weight generator that can be used runtime during development
 * It does not know about the composer structure, since thats expensive to build
 *
 * @author Nico Schoenmaker <nschoenmaker@hostnet.nl>
 */
class ReflectionGenerator
{

    private $io;

    private $environment;

    private $package_io;

    private $package_class;

    /**
     *
     * @todo We only need the "writeGeneratedFile" function of PackageIO, we
     *       should split the interface
     * @param IOInterface $io
     * @param \Twig_Environment $environment
     * @param PackageIOInterface $package_io
     * @param PackageClass $package_class
     */
    public function __construct(
        IOInterface $io,
        \Twig_Environment $environment,
        PackageIOInterface $package_io,
        PackageClass $package_class
    ) {
        $this->io            = $io;
        $this->environment   = $environment;
        $this->package_io    = $package_io;
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

        $this->package_io->writeGeneratedFile($path, $interface_name . '.php', $interface);
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
}
