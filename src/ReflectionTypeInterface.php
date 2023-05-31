<?php
/**
 * @copyright 2016-present Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Component\EntityPlugin;

interface ReflectionTypeInterface
{
    public const NON_QUALIFIED_TYPES = [
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
        'mixed',
    ];

    public function getName(): string;

    public function allowsNull(): bool;
}
