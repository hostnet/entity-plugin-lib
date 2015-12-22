<?php
namespace Hostnet\Contract\Entity\Generated;

use Hostnet\Death\Entity\DeathContractWhenProductTrait as HostnetDeathEntityBecauseProduct;
use Hostnet\Death\Entity\DeathContractWhenClientTrait as HostnetDeathEntityBecauseClient;

/**
 * Trait of traits generated for DeathContract
 * This is the guy that the main class needs to require, and ensures that everything is glued
 * together
 */
trait DeathContractTraits
{
    use HostnetDeathEntityBecauseProduct;
    use HostnetDeathEntityBecauseClient;
}
