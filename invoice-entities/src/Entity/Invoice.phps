<?php
namespace Hostnet\Invoice\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Invoice implements Generated\InvoiceInterface
{
    use Generated\InvoiceTraits;
}