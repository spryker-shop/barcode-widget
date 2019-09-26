<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\Router\Plugin\EventDispatcher;

use Silex\Provider\RoutingServiceProvider;
use Spryker\Service\Container\ContainerInterface;
use Spryker\Shared\EventDispatcher\EventDispatcherInterface;
use Spryker\Shared\EventDispatcherExtension\Dependency\Plugin\EventDispatcherPluginInterface;
use Spryker\Yves\Kernel\AbstractPlugin;
use SprykerShop\Yves\Router\Plugin\Application\RouterApplicationPlugin;
use SprykerShop\Yves\Router\Router\ChainRouter;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\EventListener\RouterListener;

/**
 * @method \SprykerShop\Yves\Router\RouterConfig getConfig()
 * @method \SprykerShop\Yves\Router\RouterFactory getFactory()
 */
class RouterListenerEventDispatcherPlugin extends AbstractPlugin implements EventDispatcherPluginInterface
{
    /**
     * {@inheritdoc}
     * - Adds a RouteListener to the EventDispatcher.
     *
     * @api
     *
     * @param \Spryker\Shared\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \Spryker\Service\Container\ContainerInterface $container
     *
     * @return \Spryker\Shared\EventDispatcher\EventDispatcherInterface
     */
    public function extend(EventDispatcherInterface $eventDispatcher, ContainerInterface $container): EventDispatcherInterface
    {
        $container->set(RoutingServiceProvider::BC_FEATURE_FLAG_ROUTER_LISTENER, false); // disables Silex RouterListener

        $eventDispatcher = $this->addSubscriber($eventDispatcher, $container);

        return $eventDispatcher;
    }

    /**
     * @param \Spryker\Shared\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \Spryker\Service\Container\ContainerInterface $container
     *
     * @return \Spryker\Shared\EventDispatcher\EventDispatcherInterface
     */
    protected function addSubscriber(EventDispatcherInterface $eventDispatcher, ContainerInterface $container): EventDispatcherInterface
    {
        $eventDispatcher->addSubscriber(new RouterListener(
            $this->getChainRouter($container),
            $this->getRequestStack($container)
        ));

        return $eventDispatcher;
    }

    /**
     * @param \Spryker\Service\Container\ContainerInterface $container
     *
     * @return \SprykerShop\Yves\Router\Router\ChainRouter
     */
    protected function getChainRouter(ContainerInterface $container): ChainRouter
    {
        return $container->get(RouterApplicationPlugin::SERVICE_CHAIN_ROUTER);
    }

    /**
     * @param \Spryker\Service\Container\ContainerInterface $container
     *
     * @return \Symfony\Component\HttpFoundation\RequestStack
     */
    protected function getRequestStack(ContainerInterface $container): RequestStack
    {
        return $container->get('request_stack');
    }
}
