<?php
namespace Hostnet\Component\EntityPlugin;

use Composer\Autoload\ClassMapGenerator;

/**
 * Concrete implementation of the PackageContentInterface
 *
 * @author Nico Schoenmaker <nschoenmaker@hostnet.nl>
 */
class PackageContent implements PackageContentInterface
{

    private $class_map              = [];
    private $entities               = null;
    private $entity_traits          = null;
    private $optional_entity_traits = null;
    private $services               = null;
    private $service_traits         = null;

    /**
     *
     * @param String $path
     * @param ClassMapperInterface $class_mapper
     *            To map the classes
     */
    public function __construct(array $class_map)
    {
        $this->class_map = $class_map;
    }

    private function warmCache()
    {
        $this->entities               = [];
        $this->entity_traits          = [];
        $this->optional_entity_traits = [];
        $this->services               = [];
        $this->service_traits         = [];

        foreach ($this->class_map as $class => $file) {
            $package_class = new PackageClass($class, $file);
            if (strstr($class, '\\Generated\\')) {
                $this->generated_files[] = $package_class;
            } elseif ($package_class->isInterface() || $package_class->isException()) {
                // Do not generate files for interfaces or exceptions
                continue;
            } elseif (strpos($class, '\\Service\\')) {
                $this->addService($package_class);
            } elseif (strpos($class, '\\Entity\\')) {
                $this->addEntity($class, $file);
            }
        }
    }

    private function addEntity($class_name, $path)
    {
        $matches = array();
        if (preg_match('/\/([A-Z][A-Za-z0-9_]+)When([A-Z][A-Za-z0-9_]+)Trait\.php$/', $path, $matches)) {
            $this->optional_entity_traits[$matches[1]][] = new OptionalPackageTrait($class_name, $path, $matches[2]);
        } else {
            $class = new PackageClass($class_name, $path);
            if ($class->isTrait()) {
                $this->entity_traits[] = $class;
            } else {
                $this->entities[] = $class;
            }
        }
    }

    private function addService(PackageClass $class)
    {
        if ($class->isTrait()) {
            $this->service_traits[] = $class;
        } else {
            $this->services[] = $class;
        }
    }

    /**
     *
     * @see \Hostnet\Component\EntityPlugin\PackageContentInterface::getEntities()
     */
    public function getEntities()
    {
        if ($this->entities === null) {
            $this->warmCache();
        }
        return $this->entities;
    }

    /**
     *
     * @see \Hostnet\Component\EntityPlugin\PackageContentInterface::getEntityOrEntityTrait()
     */
    public function getEntityOrEntityTrait($name)
    {
        if ($this->entities === null) {
            $this->warmCache();
        }
        foreach ($this->entities as $class) {
            /* @var $class PackageClass */
            if ($class->getShortName() == $name) {
                return $class;
            }
        }
        $looking_for = $name . 'Trait';
        foreach ($this->entity_traits as $class) {
            /* @var $class PackageClass */
            if ($class->getShortName() == $looking_for) {
                return $class;
            }
        }
    }

    /**
     * @see \Hostnet\Component\EntityPlugin\PackageContentInterface::getOptionalEntityTraits()
     */
    public function getOptionalEntityTraits($name)
    {
        if ($this->optional_entity_traits === null) {
            $this->warmCache();
        }

        if (isset($this->optional_entity_traits[$name])) {
            return $this->optional_entity_traits[$name];
        } else {
            return [];
        }
    }

    public function getEntityTraits()
    {
        if ($this->entity_traits === null) {
            $this->warmCache();
        }
        return $this->entity_traits;
    }

    /**
     *
     * @see \Hostnet\Component\EntityPlugin\PackageContentInterface::getServices()
     */
    public function getServices()
    {
        if ($this->services === null) {
            $this->warmCache();
        }
        return $this->services;
    }

    /**
     *
     * @see \Hostnet\Component\EntityPlugin\PackageContentInterface::getServiceTraits()
     */
    public function getServiceTraits()
    {
        if ($this->service_traits === null) {
            $this->warmCache();
        }
        return $this->service_traits;
    }

    public function hasEntity($shortName)
    {
        if ($this->entities === null) {
            $this->warmCache();
        }
        foreach ($this->entities as $entity) {
            if ($entity->getShortName() == $shortName) {
                return true;
            }
        }
        return false;
    }
}
