<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\Router\Resource;

use SprykerShop\Yves\Router\Route\RouteCollection;

class RouterResource implements ResourceInterface
{
    /**
     * @var \SprykerShop\Yves\Router\Route\RouteCollection
     */
    protected $routeCollection;

    /**
     * @var \SprykerShop\Yves\RouterExtension\Dependency\Plugin\RouteProviderPluginInterface[]
     */
    protected $routeProvider = [];

    /**
     * @param \SprykerShop\Yves\Router\Route\RouteCollection $routeCollection
     * @param \SprykerShop\Yves\RouterExtension\Dependency\Plugin\RouteProviderPluginInterface[] $routeProvider
     */
    public function __construct(RouteCollection $routeCollection, array $routeProvider)
    {
        $this->routeCollection = $routeCollection;
        $this->routeProvider = $routeProvider;
    }

    /**
     * @return \SprykerShop\Yves\Router\Route\RouteCollection
     */
    public function __invoke(): RouteCollection
    {
        foreach ($this->routeProvider as $routeProviderPlugin) {
            $this->routeCollection = $routeProviderPlugin->addRoutes($this->routeCollection);
        }

        return $this->routeCollection;
    }
}
