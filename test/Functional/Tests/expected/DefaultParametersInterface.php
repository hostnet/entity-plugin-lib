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
    public function oneParameter($bool = true);

    /**
     * @param bool   $bool
     * @param string $string
     */
    public function twoParameters($bool = true, $string = null);

    /**
     * @param bool           $bool
     * @param string         $string
     * @param \DateTime|null $date
     */
    public function threeParameters($bool = true, $string = null, \DateTime $date = null);
}
