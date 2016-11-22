<?php
namespace Hostnet\Component\EntityPlugin\Fixtures;

class ExtendedReturnType
{

    /**
     * @return ExtendedReturnType|null
     */
    public function returnTypeMethod(): ?ExtendedReturnType
    {
        return null;
    }

    /**
     * @return \DateTime|null
     */
    public function dateMethod(): ?\DateTime
    {
        return null;
    }

    /**
     * @return array|null
     */
    public function arrayMethod(): ?array
    {
        return null;
    }

    /**
     * @return self|null
     */
    public function fluentMethod(): ?self
    {
        return null;
    }

    /**
     * @return Generated\ExtendedReturnTypeInterface|null
     */
    public function fluentIfMethod(): ?Generated\ExtendedReturnTypeInterface
    {
        return null;
    }

    /**
     * @return callable|null
     */
    public function callableMethod(): ?callable
    {
        return null;
    }

    /**
     * @return resource|null
     */
    public function streamMethod(): ?resource
    {
        return null;
    }

    /**
     * @return object|null
     */
    public function objectMethod(): ?object
    {
        return null;
    }

    /**
     * @return int|null
     */
    public function intMethod(): ?int
    {
        return null;
    }

    /**
     * @return integer|null
     */
    public function integerMethod(): ?integer
    {
        return null;
    }


    /**
     * @return float|null
     */
    public function floatMethod(): ?float
    {
        return null;
    }

    /**
     * @return double|null
     */
    public function doubleMethod(): ?double
    {
        return null;
    }

    /**
     * @return string|null
     */
    public function stringMethod(): ?string
    {
        return null;
    }

    /**
     * @return bool|null
     */
    public function boolMethod(): ?bool
    {
        return null;
    }

    /**
     * @return boolean|null
     */
    public function booleanMethod(): ?boolean
    {
        return null;
    }

    /**
     * @return void
     */
    public function voidMethod(): void
    {
    }
}
