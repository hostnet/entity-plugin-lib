<?php
namespace Hostnet\Component\EntityPlugin\Fixtures;

class ScalarParams
{

    /**
     * @param int $int
     * @return int
     */
    public function intMethod(int $int)
    {
        return $int;
    }

    /**
     * @param string $string
     * @return string
     */
    public function stringMethod(string $string)
    {
        return $string;
    }

    /**
     * @param float $float
     * @return float
     */
    public function floatMethod(float $float)
    {
        return $float;
    }

    /**
     * @param bool $bool
     * @return bool
     */
    public function boolMethod(bool $bool)
    {
        return $bool;
    }

    /**
     * @param callable|null $callable
     */
    public function callableMethod(callable $callable = null)
    {
        $callable();
    }

    /**
     * @param array|null $array
     * @return array
     */
    public function arrayMethod(array $array = null): array
    {
        return [$array];
    }

    /**
     * @param int[] ...$int
     * @return int[]
     */
    public function variadicMethod(int ...$int)
    {
        return $int;
    }
}
