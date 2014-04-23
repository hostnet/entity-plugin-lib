<?php
namespace Hostnet\Component\EntityPlugin\Mock;

use Hostnet\Component\EntityPlugin\Installer as ParentInstaller;

class Installer extends ParentInstaller
{
    public $initialize_vendor_dir_called = 0;

    protected function initializeVendorDir()
    {
        $this->initialize_vendor_dir_called++;
    }
}
