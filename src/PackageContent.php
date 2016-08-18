<?php
namespace Hostnet\Component\EntityPlugin;

/**
 * Concrete implementation of the PackageContentInterface
 *
 * @author Nico Schoenmaker <nschoenmaker@hostnet.nl>
 */
class PackageContent implements PackageContentInterface
{
    const ENTITY     = '\\Entity\\';
    const REPOSITORY = '\\Repository\\';

    private $class_map;
    private $type;
    private $classes;
    private $traits;
    private $optional_traits;

    /**
     * @param array $class_map Map keyed with class names, valued where the class can be found.
     * @param string $type Either self::ENTITY or self::REPOSITORY
     */
    public function __construct(array $class_map, $type)
    {
        $this->class_map = $class_map;
        $this->type      = $type;
    }

    private function ensureCacheIsWarmed()
    {
        if ($this->classes !== null) {
            return;
        }

        $this->classes         = [];
        $this->traits          = [];
        $this->optional_traits = [];

        foreach ($this->class_map as $class_name => $file) {
            $package_class = new PackageClass($class_name, $file);

            if (! $this->isRelevant($package_class)) {
                continue;
            }

            // Now split it into a trait, optional trait or class.
            $matches = [];
            if (preg_match('/\/([A-Z][A-Za-z0-9_]+)When([A-Z][A-Za-z0-9_]+)Trait\.php$/', $file, $matches)) {
                $this->optional_traits[$matches[1]][] = new OptionalPackageTrait($class_name, $file, $matches[2]);
            } elseif ($package_class->isTrait()) {
                $this->traits[] = $package_class;
            } else {
                $this->classes[] = $package_class;
            }
        }
    }

    /**
     * Only classes in the correct namespace ($this->type) are relevant.
     *
     * Generated files, interfaces and exceptions are not relevant.
     *
     * @param PackageClass $package_class
     * @return boolean
     */
    private function isRelevant(PackageClass $package_class)
    {
        $class_name = $package_class->getName();
        if (strstr($class_name, '\\Generated\\')
            || $package_class->isInterface()
            || $package_class->isException()
            || strpos($class_name, $this->type) === false
        ) {
            return false;
        }
        return true;
    }

    /**
     * @see \Hostnet\Component\EntityPlugin\PackageContentInterface::getClasses()
     */
    public function getClasses()
    {
        $this->ensureCacheIsWarmed();
        return $this->classes;
    }

    /**
     * @see \Hostnet\Component\EntityPlugin\PackageContentInterface::getClassOrTrait()
     */
    public function getClassOrTrait($name)
    {
        $this->ensureCacheIsWarmed();
        foreach ($this->classes as $class) {
            /* @var $class PackageClass */
            if ($class->getShortName() == $name) {
                return $class;
            }
        }
        $looking_for = $name . 'Trait';
        foreach ($this->traits as $class) {
            /* @var $class PackageClass */
            if ($class->getShortName() == $looking_for) {
                return $class;
            }
        }
    }

    /**
     * @see \Hostnet\Component\EntityPlugin\PackageContentInterface::getOptionalTraits()
     */
    public function getOptionalTraits($name)
    {
        $this->ensureCacheIsWarmed();

        if (isset($this->optional_traits[$name])) {
            return $this->optional_traits[$name];
        }
        return [];
    }

    /**
     * @see \Hostnet\Component\EntityPlugin\PackageContentInterface::getTraits()
     */
    public function getTraits()
    {
        $this->ensureCacheIsWarmed();
        return $this->traits;
    }

    /**
     * @see \Hostnet\Component\EntityPlugin\PackageContentInterface::hasClass()
     */
    public function hasClass($short_name)
    {
        $this->ensureCacheIsWarmed();
        foreach ($this->classes as $entity) {
            if ($entity->getShortName() == $short_name) {
                return true;
            }
        }
        return false;
    }
}
