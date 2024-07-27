<?php

namespace DKart\Bumbu\Normalizer\AttributeStrategy;

use ReflectionProperty;

interface AttributeStrategy
{
    public static function modifyClass(string $classCode, ReflectionProperty $property): string;
}