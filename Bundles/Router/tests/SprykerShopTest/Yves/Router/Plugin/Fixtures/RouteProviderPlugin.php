<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShopTest\Yves\Router\Plugin\Fixtures;

use SprykerShop\Yves\Router\Plugin\RouteProvider\AbstractRouteProviderPlugin;
use SprykerShop\Yves\Router\Route\Route;
use SprykerShop\Yves\Router\Route\RouteCollection;

class RouteProviderPlugin extends AbstractRouteProviderPlugin
{
    /**
     * @param \SprykerShop\Yves\Router\Route\RouteCollection $routeCollection
     *
     * @return \SprykerShop\Yves\Router\Route\RouteCollection
     */
    public function addRoutes(RouteCollection $routeCollection): RouteCollection
    {
        $route = $this->buildRoute('/foo', 'Router', 'Router');
        $routeCollection->add('foo', $route);

        $route = $this->buildRoute('/', 'Router', 'Router');
        $routeCollection->add('home', $route);

        $route = $this->buildRoute('/route/{parameter}', 'Router', 'Router');
        $route->convert('parameter', function () {
            return 'converted';
        });
        $routeCollection->add('converter', $route);

        return $routeCollection;
    }

    /**
     * @param string $path
     * @param string $moduleName
     * @param string $controllerName
     * @param string $actionName
     * @param bool $parseJsonBody
     *
     * @return \SprykerShop\Yves\Router\Route\Route
     */
    protected function buildRoute(string $path, string $moduleName, string $controllerName, string $actionName = 'indexAction', bool $parseJsonBody = false): Route
    {
        $route = new Route($path);

        $template = sprintf(
            '%s/%s/%s',
            $moduleName,
            $controllerName,
            $actionName
        );

        $route->setDefault('_controller', [$this, $actionName]);
        $route->setDefault('_template', $template);

        return $route;
    }
}
