<?php
namespace Hostnet\Component\EntityPlugin\Fixtures\Generated;

/**
 * Implement this interface in ScalarParams!
 * This is a combined interface that will automatically extend to contain the required functions.
 */
interface ScalarParamsInterface
{

    /**
     * @param int $int
     * @return int
     */
    public function intMethod(int $int);

    /**
     * @param string $string
     * @return string
     */
    public function stringMethod(string $string);

    /**
     * @param float $float
     * @return float
     */
    public function floatMethod(float $float);

    /**
     * @param bool $bool
     * @return bool
     */
    public function boolMethod(bool $bool);

    /**
     * @param callable|null $callable
     */
    public function callableMethod(callable $callable = null);

    /**
     * @param array|null $array
     * @return array
     */
    public function arrayMethod(array $array = null): array;

    /**
     * @param int[] ...$int
     * @return int[]
     */
    public function variadicMethod(int ...$int);
}
