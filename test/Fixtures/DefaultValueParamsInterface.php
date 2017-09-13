<?php
namespace Hostnet\Component\EntityPlugin\Fixtures\Generated;

/**
 * Implement this interface in DefaultValueParams!
 * This is a combined interface that will automatically extend to contain the required functions.
 */
interface DefaultValueParamsInterface
{

    /**
     * @param int $int
     */
    public function defaultIntValue(int $int = 1);

    /**
     * @param int $int
     */
    public function defaultIntNullValue(int $int = null);

    /**
     * @param int $int
     */
    public function defaultIntWithoutTypeHintValue($int = 1);

    /**
     * @param float $float
     */
    public function defaultFloatValue(float $float = 1.337);

    /**
     * @param float $float
     */
    public function defaultFloatWithoutTypeHintValue($float = 1.337);

    /**
     * @param float $float
     */
    public function defaultFloatNullValue(float $float = null);

    /**
     * @param bool $bool
     */
    public function defaultBoolFalseValue(bool $bool = false);

    /**
     * @param bool $bool
     */
    public function defaultBoolTrueValue(bool $bool = true);

    /**
     * @param bool $bool
     */
    public function defaultBoolNullValue(bool $bool = null);

    /**
     * @param string $string
     */
    public function defaultStringValue(string $string = 'string');

    /**
     * @param string $string
     */
    public function defaultStringValueHereDoc(string $string = 'test');

    /**
     * @param string $string
     */
    public function defaultStringValueNowDoc(string $string = 'test');

    /**
     * @param string $string
     */
    public function defaultNastyStringValue(string $string = '""');

    /**
     * @param string $string
     */
    public function defaultNullStringValue(string $string = 'null');

    /**
     * @param string $string
     */
    public function defaultFunkyStringValue(string $string = '\'\'');

    /**
     * @param string $string
     */
    public function defaultEscapedSingleQuoteStringValue(string $string = '\'');

    /**
     * @param string $string
     */
    public function defaultBackslashNStringValue(string $string = '\\n');

    /**
     * @param string $string
     */
    public function defaultNewLineStringValue(string $string = '
');

    /**
     * @param string $string
     */
    public function defaultStringNullValue(string $string = null);

    /**
     * @param string $string
     */
    public function defaultStringWithoutTypeHint($string = 'string');

    /**
     * @param string $string
     */
    public function defaultStringWhichIsInt(string $string = '1');

    /**
     * @param string $string
     */
    public function defaultStringConstantRoot($string = \DateTime::ATOM);

    /**
     * @param string $string
     */
    public function defaultStringConstantSelf($string = self::FOO);

    /**
     * @param string $string
     */
    public function defaultStringConstant($string = \Hostnet\Component\EntityPlugin\Fixtures\DefaultValueParams::FOO);

    /**
     * @param array $array
     */
    public function defaultArrayValue(array $array = []);

    /**
     * @param array $array
     */
    public function defaultArrayOldValue(array $array = []);

    /**
     * @param array $array
     */
    public function defaultArrayNullValue(array $array = null);

    /**
     * @param \DateTime|null $date_time
     */
    public function defaultDateTimeNullValue(\DateTime $date_time = null);

    /**
     * @param callable $date_time
     */
    public function defaultCallableNullValue(callable $callable = null);

    /**
     * Docs
     */
    public function defaultSelfNullValue(self $self = null);
}
