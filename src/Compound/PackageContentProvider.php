<?php
namespace Hostnet\Component\EntityPlugin\Compound;

use Hostnet\Component\EntityPlugin\EntityPackage;
use Hostnet\Component\EntityPlugin\PackageContent;

/**
 * Switches the package content between entities and repositories
 */
class PackageContentProvider
{
    private $type;

    /**
     * @param string $type One of PackageContent::ENTITY or PackageContent::REPOSITORY
     */
    public function __construct($type)
    {
        $this->type = $type;
    }

    /**
     * @param EntityPackage $entity_package
     * @return PackageContent
     */
    public function getPackageContent(EntityPackage $entity_package)
    {
        return $this->type == PackageContent::ENTITY
            ? $entity_package->getEntityContent()
            : $entity_package->getRepositoryContent();
    }
}
