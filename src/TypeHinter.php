<?php
namespace Hostnet\Component\EntityPlugin;

/**
 * Ugly hack to circumvent PHP autoloading the class that is typehinted
 * As it might not be autoloadable install-time
 * Examples of \ReflectionParameter->__toString():
 * Parameter #0 [ <required> DateTime $date ]
 * Parameter #0 [ <optional> DateTime or NULL $date = NULL ]
 * Parameter #0 [ <required> Hostnet\Component\EntityPlugin\ReflectionGenerator $generator ]
 */
class TypeHinter
{

    public function getTypeHint(\ReflectionParameter $parameter)
    {
        if ($parameter->isArray()) {
            return 'array ';
        }
        $matches = [];
        preg_match('/\[\s\<\w+?>\s([\\\\\w]+)/s', $parameter->__toString(), $matches);
        if (isset($matches[1])) {
            return '\\' . $matches[1] . ' ';
        }
        return '';
    }
}
