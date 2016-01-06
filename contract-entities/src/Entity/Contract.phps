<?php
namespace Hostnet\Contract\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Contract implements Generated\ContractInterface
{
    use Generated\ContractTraits;

    /**
     * @ORM\ManyToOne(targetEntity="Client",
     *               inversedBy="contracts")
     */
     private $client;
}