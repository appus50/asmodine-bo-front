<?php

namespace Asmodine\FrontBundle\Doctrine;

use Doctrine\ORM\Mapping\DefaultEntityListenerResolver;
use Psr\Container\ContainerInterface;

/**
 * Class EntityListenerResolver.
 */
class EntityListenerResolver extends DefaultEntityListenerResolver
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $mapping;

    /**
     * EntityListenerResolver constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->mapping = [];
    }

    /**
     * @param $className
     * @param $service
     */
    public function addMapping($className, $service)
    {
        $this->mapping[$className] = $service;
    }

    /**
     * @see DefaultEntityListenerResolver::resolve()
     *
     * @param string $className
     *
     * @return mixed|object
     */
    public function resolve($className)
    {
        if (isset($this->mapping[$className]) && $this->container->has($this->mapping[$className])) {
            return $this->container->get($this->mapping[$className]);
        }

        return parent::resolve($className);
    }
}
