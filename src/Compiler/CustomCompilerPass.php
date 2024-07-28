<?php

namespace DKart\Bumbu\Compiler;

use DKart\Bumbu\Attribute\Getter;
use DKart\Bumbu\Attribute\Setter;
use DKart\Bumbu\Compiler\Proxy\ProxyFactory;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CustomCompilerPass implements CompilerPassInterface
{
    const ATTRIBUTES = [
        Getter::class => AttributeStrategy\Getter::class,
        Setter::class => AttributeStrategy\Setter::class
    ];

    public function process(ContainerBuilder $container)
    {
        foreach ($container->getDefinitions() as $id => $definition) {
            $class = $definition->getClass();

            if ($class && class_exists($class)) {
                $reflectionClass = new ReflectionClass($class);

                foreach ($reflectionClass->getProperties() as $property) {
                    foreach ($property->getAttributes() as $attribute) {

                        if (in_array($attribute->getName(), array_keys(self::ATTRIBUTES))) {

                            $proxyClass = (new ProxyFactory())->generateProxyClass(
                                $reflectionClass, self::ATTRIBUTES, $container);

                            $definition->setClass($proxyClass);
                        }
                    }
                }
            }
        }
    }
}