<?php
namespace Hostnet\Component\EntityPlugin\Fixtures\Generated;

/**
 * Implement this interface in NullableParams!
 * This is a combined interface that will automatically extend to contain the required functions.
 */
interface NullableParamsInterface
{

    /**
     * @param int|null $start
     */
    public function nullableParam(?int $start);

    /**
     * @param int|null $start
     */
    public function defaultNullParam(int $start = null);

    /**
     * @param int|null $start
     */
    public function defaultNullNullableParam(int $start = null);

    /**
     * @param int|null $start
     */
    public function defaultValueNullableParam(?int $start = null);

    /**
     * @param string|null $start
     */
    public function defaultValueStringNullNullableParam(?string $start = null);

    /**
     * @param int|null $start
     */
    public function nullableReference(?int &$start = null);

    /**
     * @param int[]|null[] ...$start
     */
    public function nullableVariadic(int &...$start);
}
