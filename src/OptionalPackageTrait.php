<?php
/**
 * @copyright 2014-present Hostnet B.V.
 */
declare(strict_types=1);

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
     * @param string $class
     * @param string $path
     * @param string $requirement
     */
    public function __construct($class, $path, string $requirement)
    {
        parent::__construct($class, $path);
        $this->requirement = $requirement;
    }

    public function getRequirement(): string
    {
        return $this->requirement;
    }

    public function getAlias(): string
    {
        return parent::getAlias() . 'Because' . $this->getRequirement();
    }
}
