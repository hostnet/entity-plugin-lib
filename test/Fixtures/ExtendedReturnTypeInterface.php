<?php
namespace Hostnet\Component\EntityPlugin\Fixtures\Generated;

/**
 * Implement this interface in ExtendedReturnType!
 * This is a combined interface that will automatically extend to contain the required functions.
 */
interface ExtendedReturnTypeInterface
{

    /**
     * @return \Hostnet\Component\EntityPlugin\Fixtures\ExtendedReturnType|null
     */
    public function returnTypeMethod(): ?\Hostnet\Component\EntityPlugin\Fixtures\ExtendedReturnType;

    /**
     * @return \DateTime|null
     */
    public function dateMethod(): ?\DateTime;

    /**
     * @return array|null
     */
    public function arrayMethod(): ?array;

    /**
     * @return self|null
     */
    public function fluentMethod();

    /**
     * @return ExtendedReturnTypeInterface|null
     */
    public function fluentIfMethod(): ?\Hostnet\Component\EntityPlugin\Fixtures\Generated\ExtendedReturnTypeInterface;

    /**
     * @return callable|null
     */
    public function callableMethod(): ?callable;

    /**
     * @return \Hostnet\Component\EntityPlugin\Fixtures\resource|null
     */
    public function streamMethod(): ?\Hostnet\Component\EntityPlugin\Fixtures\resource;

    /**
     * @return \Hostnet\Component\EntityPlugin\Fixtures\object|null
     */
    public function objectMethod(): ?\object;

    /**
     * @return int|null
     */
    public function intMethod(): ?int;

    /**
     * @return \Hostnet\Component\EntityPlugin\Fixtures\integer|null
     */
    public function integerMethod(): ?\Hostnet\Component\EntityPlugin\Fixtures\integer;

    /**
     * @return float|null
     */
    public function floatMethod(): ?float;

    /**
     * @return \Hostnet\Component\EntityPlugin\Fixtures\double|null
     */
    public function doubleMethod(): ?\Hostnet\Component\EntityPlugin\Fixtures\double;

    /**
     * @return string|null
     */
    public function stringMethod(): ?string;

    /**
     * @return bool|null
     */
    public function boolMethod(): ?bool;

    /**
     * @return \Hostnet\Component\EntityPlugin\Fixtures\boolean|null
     */
    public function booleanMethod(): ?\Hostnet\Component\EntityPlugin\Fixtures\boolean;

    /**
     * @return void
     */
    public function voidMethod(): void;
}
