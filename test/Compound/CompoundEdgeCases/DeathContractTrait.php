<?php
namespace Hostnet\Contract\Entity\Generated;

use Hostnet\Death\Entity\DeathContractWhenClientTrait as HostnetDeathEntityBecauseClient;
use Hostnet\Death\Entity\DeathContractWhenProductTrait as HostnetDeathEntityBecauseProduct;

/**
 * Trait of traits generated for DeathContract
 * This is the guy that the main class needs to require, and ensures that everything is glued
 * together
 */
trait DeathContractTrait
{
    use HostnetDeathEntityBecauseClient;
    use HostnetDeathEntityBecauseProduct;
}
