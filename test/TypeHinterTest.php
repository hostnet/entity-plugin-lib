<?php
namespace Hostnet\Component\EntityPlugin;

use \Hostnet\Component\EntityPlugin\ReflectionGenerator;
use PHPUnit\Framework\TestCase;

function parameterHints(
    array $array,
    \Hostnet\Component\EntityPlugin\ReflectionGenerator $full_namespace,
    ReflectionGenerator $namespace,
    \DateTime $datetime,
    $empty,
    \DateTime $datetime_null = null
) {
    return 'quite useless, we only need the parameters...';
}

/**
 * @covers Hostnet\Component\EntityPlugin\TypeHinter
 */
class TypeHinterTest extends TestCase
{

    /**
     * @dataProvider getTypeHintProvider
     */
    public function testGetTypeHint(\ReflectionParameter $parameter, $expected)
    {
        $hinter = new TypeHinter();
        $this->assertEquals($expected, $hinter->getTypeHint($parameter));
    }

    public function getTypeHintProvider()
    {
        return [
            [
                new \ReflectionParameter('Hostnet\Component\EntityPlugin\parameterHints', 'array'),
                'array '
            ],
            [
                new \ReflectionParameter('Hostnet\Component\EntityPlugin\parameterHints', 'datetime_null'),
                '\DateTime '
            ],
            [
                new \ReflectionParameter('Hostnet\Component\EntityPlugin\parameterHints', 'datetime'),
                '\DateTime '
            ],
            [
                new \ReflectionParameter('Hostnet\Component\EntityPlugin\parameterHints', 'namespace'),
                '\Hostnet\Component\EntityPlugin\ReflectionGenerator '
            ],
            [
                new \ReflectionParameter('Hostnet\Component\EntityPlugin\parameterHints', 'full_namespace'),
                '\Hostnet\Component\EntityPlugin\ReflectionGenerator '
            ],
            [
                new \ReflectionParameter('Hostnet\Component\EntityPlugin\parameterHints', 'empty'),
                ''
            ]
        ];
    }
}
