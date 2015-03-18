<?php
namespace Hostnet\Component\EntityPlugin;

/**
 * This models the distinction between the class name and the generated location
 * for optional loaded entity traits.
 */
class OptionalPackageTrait extends PackageClass
{

    /**
     * Shortnames of the classes required to exists to load this trait
     * @var string $requirements[]
     */
    private $requirement;

    /**
     *
     * @param string $class
     * @param string $path
     * @param string $requirement
     */
    public function __construct($class, $path, $requirement)
    {
        parent::__construct($class, $path);
        $this->requirement = $requirement;
    }

    public function getRequirement()
    {
        return $this->requirement;
    }

    public function getAlias()
    {
        return parent::getAlias() . 'Because' . $this->getRequirement();
    }
}
