<?php
namespace Hostnet\Component\EntityPlugin;

/**
 * Generates the Abstract*Trait and *TraitInterface
 * As the class name tells you, they are generated with a empty class body
 *
 * Why on earth would we do this?
 * Client\Entity\Client uses Client\Entity\Generated\ClientTraits
 * Client\Entity\Generated\ClientTraits uses AdvancedClient\Entity\ClientTrait
 * AdvancedClient\Entity\ClientTrait uses Client\Entity\Generated\AbstractClientTrait
 * Client\Entity\Generated\AbstractClientTrait is generated based on reflection of Client\Entity\Client
 * This is a circle. There are others. Thus we generate empty files first where we have to.
 * @author Nico Schoenmaker <nschoenmaker@hostnet.nl>
 */
class EmptyGenerator extends ReflectionGenerator
{
  /**
   * @see \Hostnet\Component\EntityPlugin\ReflectionGenerator::getMethods()
   */
  protected function getMethods($namespace, $trait_or_class_name)
  {
    return array();
  }
}