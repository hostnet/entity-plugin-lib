<?php
namespace Hostnet\Client\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Client implements Generated\ClientInterface
{
    use Generated\ClientTraits;
}