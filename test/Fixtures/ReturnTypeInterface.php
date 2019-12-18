<?php
namespace Hostnet\Component\EntityPlugin\Fixtures\Generated;

/**
 * Implement this interface in ReturnType!
 * This is a combined interface that will automatically extend to contain the required functions.
 */
interface ReturnTypeInterface
{

    /**
     * @return \Hostnet\Component\EntityPlugin\Fixtures\ReturnType
     */
    public function returnTypeMethod(): \Hostnet\Component\EntityPlugin\Fixtures\ReturnType;

    /**
     * @return \DateTime
     */
    public function dateMethod(): \DateTime;

    /**
     * @return array
     */
    public function arrayMethod(): array;

    /**
     * @return self
     */
    public function fluentMethod();

    /**
     * @return ReturnTypeInterface
     */
    public function fluentInterfaceMethod(): \Hostnet\Component\EntityPlugin\Fixtures\Generated\ReturnTypeInterface;

    /**
     * @return callable
     */
    public function callableMethod(): callable;

    /**
     * @return \Hostnet\Component\EntityPlugin\Fixtures\resource
     */
    public function streamMethod(): \Hostnet\Component\EntityPlugin\Fixtures\resource;

    /**
     * @return \Hostnet\Component\EntityPlugin\Fixtures\object
     */
    public function objectMethod(): \object;

    /**
     * @return int
     */
    public function intMethod(): int;

    /**
     * @return \Hostnet\Component\EntityPlugin\Fixtures\integer
     */
    public function integerMethod(): \Hostnet\Component\EntityPlugin\Fixtures\integer;

    /**
     * @return float
     */
    public function floatMethod(): float;

    /**
     * @return \Hostnet\Component\EntityPlugin\Fixtures\double
     */
    public function doubleMethod(): \Hostnet\Component\EntityPlugin\Fixtures\double;

    /**
     * @return string
     */
    public function stringMethod(): string;

    /**
     * @return bool
     */
    public function boolMethod(): bool;

    /**
     * @return \Hostnet\Component\EntityPlugin\Fixtures\boolean
     */
    public function booleanMethod(): \Hostnet\Component\EntityPlugin\Fixtures\boolean;

    /**
     * @return \Hostnet\Component\EntityPlugin\Fixtures\boolean|\stdClass false or instance of \stdClass
     */
    public function typeWithCommentMethod();
}
