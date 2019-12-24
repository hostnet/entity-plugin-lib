<?php
namespace Hostnet\Component\EntityPlugin\Fixtures;

trait ExtraTrait
{
    /**
     * I am from a trait
     */
    public function extra()
    {
        return $this;
    }
}
