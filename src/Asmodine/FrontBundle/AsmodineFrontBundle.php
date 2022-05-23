<?php

namespace Asmodine\FrontBundle;

use Asmodine\FrontBundle\DependencyInjection\Compiler\DoctrineEntityListenerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class AsmodineFrontBundle.
 */
class AsmodineFrontBundle extends Bundle
{
    /**
     * @see Bundle::build()
     *
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new DoctrineEntityListenerPass());
    }
}
