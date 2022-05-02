<?php
/**
 * @copyright 2015-present Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Component\EntityPlugin\Compound;

use Composer\Package\Package;
use Hostnet\Component\EntityPlugin\EntityPackage;
use Hostnet\Component\EntityPlugin\PackageContent;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hostnet\Component\EntityPlugin\Compound\PackageContentProvider
 * @covers \Hostnet\Component\EntityPlugin\Compound\PackageContentProvider
 */
class PackageContentProviderTest extends TestCase
{
    public function testGetPackageContent(): void
    {
        $package        = new Package('hostnet/package', '1.0.0', '1.0.0');
        $entity_content = new PackageContent([], PackageContent::ENTITY);
        $repo_content   = new PackageContent([], PackageContent::REPOSITORY);
        $entity_package = new EntityPackage($package, $entity_content, $repo_content);

        $provider = new PackageContentProvider(PackageContent::ENTITY);
        $this->assertSame($entity_content, $provider->getPackageContent($entity_package));

        $provider = new PackageContentProvider(PackageContent::REPOSITORY);
        $this->assertSame($repo_content, $provider->getPackageContent($entity_package));
    }
}
