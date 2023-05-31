<?php
namespace Hostnet\Component\EntityPlugin\Fixtures;

class ReflectionReturnSelf
{
    public function docBlock(): self
    {
        return new self();
    }
}
