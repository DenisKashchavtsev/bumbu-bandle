<?php

namespace DKart\Bumbu\Compiler\AttributeStrategy;

use ReflectionProperty;

class Getter implements AttributeStrategy
{
    private const TEMPLATE = '
    public function get<Name>(): <null><type>
    {
        return $this-><name>;
    }';

    public static function modifyClass(string $classCode, ReflectionProperty $property): string
    {
        $placeholders = [
            '<Name>' => ucwords($property->getName()),
            '<name>' => $property->getName(),
            '<null>' => $property->getType()->allowsNull() ? '?' : '',
            '<type>' => $property->getType()->getName(),
        ];

        $proxyCode = strtr(self::TEMPLATE, $placeholders);

        $pattern = '/\}.*$/';

        $replacement = $proxyCode . PHP_EOL . '}';

        return preg_replace($pattern, $replacement, $classCode);
    }
}