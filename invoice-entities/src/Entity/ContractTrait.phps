<?php
namespace Hostnet\Invoice\Entity;

use Doctrine\ORM\Mapping as ORM;

trait ContractTrait
{
    /**
     * @ORM\ManyToMany(targetEntity="Invoice",
     *                 inversedBy="contracts")
     */
    private $invoices;
}