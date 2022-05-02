<?php
/**
 * @copyright 2016-present Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Component\EntityPlugin;

use Composer\Package\PackageInterface;

/**
 * A thing able to resolve the install dir for a PackageInterface
 */
interface PackagePathResolverInterface
{
    /**
     * Get the path containing the actual source of the application
     *
     * @param PackageInterface $package
     */
    public function getSourcePath(PackageInterface $package): string;
}
