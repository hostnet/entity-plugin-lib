<?php
namespace Hostnet\Component\EntityPlugin\Fixtures;

class ReturnType implements Generated\ReturnTypeInterface
{

    /**
     * @return ReturnType
     */
    public function returnTypeMethod(): ReturnType
    {
        return new ReturnType();
    }

    /**
     * @return \DateTime
     */
    public function dateMethod(): \DateTime
    {
        return new \DateTime();
    }

    /**
     * @return array
     */
    public function arrayMethod(): array
    {
        return [];
    }

    /**
     * @return self
     */
    public function fluentMethod(): self
    {
        return $this;
    }

    /**
     * @return Generated\ReturnTypeInterface
     */
    public function fluentInterfaceMethod(): Generated\ReturnTypeInterface
    {
        return $this;
    }

    /**
     * @return callable
     */
    public function callableMethod(): callable
    {
        return function () {
            return null;
        };
    }

    /**
     * @return resource
     */
    public function streamMethod(): resource
    {
        return new resource(fopen('php://memory', 'r'));
    }

    /**
     * @return object
     */
    public function objectMethod(): object
    {
        return new object();
    }

    /**
     * @return int
     */
    public function intMethod(): int
    {
        return 1;
    }

    /**
     * @return integer
     */
    public function integerMethod(): integer
    {
        return new integer(1);
    }


    /**
     * @return float
     */
    public function floatMethod(): float
    {
        return 4.5;
    }

    /**
     * @return double
     */
    public function doubleMethod(): double
    {
        return 4.5;
    }

    /**
     * @return string
     */
    public function stringMethod(): string
    {
        return 'string';
    }

    /**
     * @return bool
     */
    public function boolMethod(): bool
    {
        return true;
    }

    /**
     * @return boolean
     */
    public function booleanMethod(): boolean
    {
        return new boolean(true);
    }

    /**
     * @return boolean|\stdClass false or instance of \stdClass
     */
    public function typeWithCommentMethod()
    {
        return false;
    }
}
