<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\Router;

use Spryker\Yves\Kernel\AbstractBundleDependencyProvider;
use Spryker\Yves\Kernel\Container;

/**
 * @method \SprykerShop\Yves\Router\RouterConfig getConfig()
 */
class RouterDependencyProvider extends AbstractBundleDependencyProvider
{
    public const ROUTER_PLUGINS = 'router-plugins';
    public const ROUTER_ROUTE_PROVIDER = 'router-controller-provider';
    public const POST_ADD_ROUTE_MANIPULATOR = 'route manipulator';
    public const ROUTER_ENHANCER_PLUGINS = 'router enhancer plugin';

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    public function provideDependencies(Container $container)
    {
        $container = $this->addRouterPlugins($container);
        $container = $this->addRouterEnhancerPlugins($container);
        $container = $this->addRouteProvider($container);
        $container = $this->addPostAddRouteManipulator($container);

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addRouterPlugins(Container $container): Container
    {
        $container->set(static::ROUTER_PLUGINS, function () {
            return $this->getRouterPlugins();
        });

        return $container;
    }

    /**
     * @return \SprykerShop\Yves\RouterExtension\Dependency\Plugin\RouterPluginInterface[]
     */
    protected function getRouterPlugins(): array
    {
        return [];
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addRouterEnhancerPlugins(Container $container): Container
    {
        $container->set(static::ROUTER_ENHANCER_PLUGINS, function () {
            return $this->getRouterEnhancerPlugins();
        });

        return $container;
    }

    /**
     * @return \SprykerShop\Yves\RouterExtension\Dependency\Plugin\RouterEnhancerPluginInterface[]
     */
    protected function getRouterEnhancerPlugins(): array
    {
        return [];
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addRouteProvider(Container $container): Container
    {
        $container->set(static::ROUTER_ROUTE_PROVIDER, function () {
            return $this->getRouteProvider();
        });

        return $container;
    }

    /**
     * @return \SprykerShop\Yves\RouterExtension\Dependency\Plugin\RouterPluginInterface[]
     */
    protected function getRouteProvider(): array
    {
        return [];
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addPostAddRouteManipulator(Container $container): Container
    {
        $container->set(static::POST_ADD_ROUTE_MANIPULATOR, function () {
            return $this->getPostAddRouteManipulator();
        });

        return $container;
    }

    /**
     * @return \SprykerShop\Yves\RouterExtension\Dependency\Plugin\PostAddRouteManipulatorPluginInterface[]
     */
    protected function getPostAddRouteManipulator(): array
    {
        return [];
    }
}
