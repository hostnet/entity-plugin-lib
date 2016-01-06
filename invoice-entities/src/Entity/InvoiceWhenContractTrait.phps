<?php
namespace Hostnet\Invoice\Entity;

use Doctrine\ORM\Mapping as ORM;

trait InvoiceWhenContractTrait
{
     /**
     * @ORM\ManyToOne(targetEntity="Contract", mappedBy="invoices")
     */
    private $contracts;
}