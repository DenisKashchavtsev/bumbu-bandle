<?php

namespace DKart\Bumbu;

use App\Kernel;
use DKart\Bumbu\Compiler\CustomCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\KernelInterface;

class BumbuBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new CustomCompilerPass());
    }
}