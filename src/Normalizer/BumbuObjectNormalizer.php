<?php

namespace DKart\Bumbu\Normalizer;

use DKart\Bumbu\Attribute\Getter;
use DKart\Bumbu\Attribute\Setter;
use DKart\Bumbu\Normalizer\Proxy\ProxyFactory;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class BumbuObjectNormalizer extends ObjectNormalizer
{
    const ATTRIBUTES = [
        Getter::class => AttributeStrategy\Getter::class,
        Setter::class => AttributeStrategy\Setter::class
    ];

    public function __construct(
        private readonly ProxyFactory    $proxyFactory,
        private readonly KernelInterface $kernel
    )
    {
        parent::__construct();
    }

    /**
     * @throws ReflectionException
     */
    protected function instantiateObject(array &$data, $class, array &$context, ReflectionClass $reflectionClass, $allowedAttributes, string $format = null): object
    {
        foreach ($reflectionClass->getProperties() as $property) {
            foreach ($property->getAttributes() as $attribute) {
                if (in_array($attribute->getName(), array_keys(self::ATTRIBUTES))) {

                    $reflection = new ReflectionClass($this->proxyFactory->generateProxyClass(
                        $reflectionClass, $this->kernel, self::ATTRIBUTES));

                    return parent::instantiateObject($data, $reflection->getName(), $context, $reflection, $allowedAttributes, $format);
                }
            }
        }

        return parent::instantiateObject($data, $class, $context, $reflectionClass, $allowedAttributes, $format);
    }
}