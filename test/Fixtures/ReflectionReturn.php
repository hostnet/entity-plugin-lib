<?php
namespace Hostnet\Component\EntityPlugin\Fixtures;

class ReflectionReturn
{
    /**
     * A nice Doc Block
     *
     * @param array $left
     * @param array $right
     * @return array
     */
    public function docBlock(array $left, array $right): array
    {
        return array_merge($left, $right);
    }
}
