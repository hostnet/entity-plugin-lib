<?php
namespace Hostnet\Component\EntityPlugin;

use \Hostnet\Component\EntityPlugin\ReflectionGenerator;

function parameterHints(
    array $array,
    \Hostnet\Component\EntityPlugin\ReflectionGenerator $full_namespace,
    ReflectionGenerator $namespace,
    \DateTime $datetime,
    \DateTime $datetime_null = null
) {
    return "quite useless, we only need the parameters...";
}

class TypeHinterTest extends \PHPUnit_Framework_TestCase
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
        return array(
            array(
                new \ReflectionParameter('Hostnet\Component\EntityPlugin\parameterHints', 'array'),
                'array '
            ),
            array(
                new \ReflectionParameter('Hostnet\Component\EntityPlugin\parameterHints', 'datetime_null'),
                '\DateTime '
            ),
            array(
                new \ReflectionParameter('Hostnet\Component\EntityPlugin\parameterHints', 'datetime'),
                '\DateTime '
            ),
            array(
                new \ReflectionParameter('Hostnet\Component\EntityPlugin\parameterHints', 'namespace'),
                '\Hostnet\Component\EntityPlugin\ReflectionGenerator '
            ),
            array(
                new \ReflectionParameter('Hostnet\Component\EntityPlugin\parameterHints', 'full_namespace'),
                '\Hostnet\Component\EntityPlugin\ReflectionGenerator '
            )

        );
    }
}
