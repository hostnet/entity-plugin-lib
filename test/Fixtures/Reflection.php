<?php
namespace Hostnet\Component\EntityPlugin\Fixtures;

use Hostnet\FunctionalFixtures\Entity\ExtraTrait;

class Reflection
{
    use ExtraTrait;

    /**
     * Blaah Blaah Blaaaah Cloud...
     *
     * @param Generated\Foo $foo
     * @param unknown $empty
     * @param \Exception $fully_qualified
     * @param array[] $array_of_arrays
     * @return Generated\Blyp
     */
    public function docBlock($param_1, array $param_2, \Exception $fully_qualified)
    {
        return 'quite useless, we only need the docblock...';
    }

    public static function getExpected()
    {
        return <<<'EOS'
/**
     * Blaah Blaah Blaaaah Cloud...
     *
     * @param Foo $foo
     * @param \Hostnet\Component\EntityPlugin\Fixtures\unknown $empty
     * @param \Exception $fully_qualified
     * @param array[] $array_of_arrays
     * @return Blyp
     */
EOS;
    }

    /** @param ~~~\o/~~~ $param_1 */
    public function invalidDocBlock($param_1)
    {
    }
}
