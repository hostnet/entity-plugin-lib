<?php
namespace Hostnet\Component\EntityPlugin;

interface ReflectionTypeInterface
{
    const NON_QUALIFIED_TYPES = [
        'null',
        'void',
        'self',
        'array',
        'callable',
        'iterable',
        'bool',
        'float',
        'int',
        'string',
    ];

    public function getName();

    public function allowsNull();
}
