<?php

namespace DKart\Bumbu\Normalizer\AttributeStrategy;

use ReflectionProperty;

class Setter implements AttributeStrategy
{
    private const TEMPLATE = '
    public function set<Name>(<null><type> $<name>): self 
    {
        $this-><name> = $<name>;
        
        return $this;
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