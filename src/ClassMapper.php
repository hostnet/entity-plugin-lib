<?php
namespace Hostnet\Component\EntityPlugin;

use Composer\Autoload\ClassMapGenerator;

/**
 * Concrete classmapper that utilizes the ClassMapGenerator from composer
 */
class ClassMapper implements ClassMapperInterface
{

    /**
     *
     * @see \Hostnet\Component\EntityPlugin\ClassMapperInterface::createClassMap()
     */
    public function createClassMap($path)
    {
        return ClassMapGenerator::createMap($path);
    }
}
