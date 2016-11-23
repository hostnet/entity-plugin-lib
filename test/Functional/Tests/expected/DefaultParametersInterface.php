<?php
namespace Hostnet\FunctionalFixtures\Entity\Generated;

/**
 * Implement this interface in DefaultParameters!
 * This is a combined interface that will automatically extend to contain the required functions.
 */
interface DefaultParametersInterface
{

    /**
     * @param bool $bool
     */
    public function oneParameter($bool = null);

    /**
     * @param bool   $bool
     * @param string $string
     */
    public function twoParameters($bool = null, $string = null);

    /**
     * @param bool           $bool
     * @param string         $string
     * @param \DateTime|null $date
     */
    public function threeParameters($bool = null, $string = null, \DateTime $date = null);
}
