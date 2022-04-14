<?php
/**
 * @copyright 2015-present Hostnet B.V.
 */
declare(strict_types=1);

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
     */
    public function getPackageContent(EntityPackage $entity_package): PackageContent
    {
        if ($this->type == PackageContent::ENTITY) {
            return $entity_package->getEntityContent();
        } else {
            return $entity_package->getRepositoryContent();
        }
    }
}
