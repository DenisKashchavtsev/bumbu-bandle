<?php

declare(strict_types=1);

namespace DKart\Bumbu\Compiler\Proxy;

use ReflectionClass;
use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * This factory is used to create proxy objects for classes at runtime.
 */
class ProxyFactory
{
    const CACHE_DIR = '/bumbu/proxies';
    const CLASS_PREFIX = 'Proxy';
    private ReflectionClass $reflectionClass;
    private ContainerBuilder $container;

    public function generateProxyClass(ReflectionClass $reflectionClass, array $attributes, ContainerBuilder $container): string
    {
        $this->reflectionClass = $reflectionClass;
        $this->container = $container;

        $classCode = file_get_contents($reflectionClass->getFileName());
        $classCode = $this->modifyClassName($classCode);

        foreach ($reflectionClass->getProperties() as $property) {
            foreach ($attributes as $attribute => $attributeStrategy) {
                if ($property->getAttributes($attribute)) {
                    $classCode = $attributeStrategy::modifyClass($classCode, $property);
                }
            }
        }

        $this->saveToFile($classCode);

        return $this->runProxyClass();

    }

    private function modifyClassName(string $classCode): string
    {
        $pattern = '/\bclass\s+\w+\s*{.*$/s';

        $namespace = explode('\\', $this->reflectionClass->getName());
        $className = end($namespace);

        $replacement = 'class ' . $className . self::CLASS_PREFIX
            . ' extends ' . $className . PHP_EOL . ' {' . PHP_EOL . '}';

        return preg_replace($pattern, $replacement, $classCode);
    }

    public function saveToFile(string $classCode): bool
    {
        $directory = dirname($this->getProxyPath());

        if (!is_dir($directory)) {
            if (!mkdir($directory, 0755, true) && !is_dir($directory)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $directory));
            }
        }

        return file_put_contents($this->getProxyPath(), $classCode) !== false;
    }

    private function getProxyPath(): string
    {
        return $this->container->getParameter('kernel.cache_dir')
            . self::CACHE_DIR
            . str_replace(
                $this->container->getParameter('kernel.project_dir'),
                '',
                $this->reflectionClass->getFileName());
    }

    private function runProxyClass(): string
    {
        if (!class_exists($this->getProxyName())) {
            require $this->getProxyPath();
        }

        return $this->getProxyName();
    }

    private function getProxyName(): string
    {
        return $this->reflectionClass->getName() . self::CLASS_PREFIX;
    }
}
