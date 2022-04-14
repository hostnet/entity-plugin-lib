<?php
/**
 * @copyright 2014-present Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Component\EntityPlugin\Mock;

use Hostnet\Component\EntityPlugin\Installer as ParentInstaller;

class Installer extends ParentInstaller
{
    public $initialize_vendor_dir_called = 0;

    protected function initializeVendorDir(): void
    {
        $this->initialize_vendor_dir_called++;
    }
}
