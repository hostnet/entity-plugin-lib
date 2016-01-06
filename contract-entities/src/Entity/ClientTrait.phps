<?php
namespace Hostnet\Contract\Entity;

use Doctrine\ORM\Mapping as ORM;

trait ClientTrait {
    /**
     * @ORM\OneToMany(targetEntity="Contract",
     *                mappedBy="client")
     */
    private $contracts;
}