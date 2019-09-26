<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\AgentPage\Plugin\Router;

use SprykerShop\Yves\Router\Plugin\RouteProvider\AbstractRouteProviderPlugin;
use SprykerShop\Yves\Router\Route\RouteCollection;

class AgentPageRouteProviderPlugin extends AbstractRouteProviderPlugin
{
    protected const ROUTE_LOGIN = 'agent/login';
    protected const ROUTE_AGENT_OVERVIEW = 'agent/overview';

    /**
     * Specification:
     * - Adds Routes to the RouteCollection.
     *
     * @api
     *
     * @param \SprykerShop\Yves\Router\Route\RouteCollection $routeCollection
     *
     * @return \SprykerShop\Yves\Router\Route\RouteCollection
     */
    public function addRoutes(RouteCollection $routeCollection): RouteCollection
    {
        $routeCollection = $this->addLoginRoute($routeCollection);
        $routeCollection = $this->addOverviewRoute($routeCollection);

        return $routeCollection;
    }

    /**
     * @param \SprykerShop\Yves\Router\Route\RouteCollection $routeCollection
     *
     * @return \SprykerShop\Yves\Router\Route\RouteCollection
     */
    protected function addLoginRoute(RouteCollection $routeCollection): RouteCollection
    {
        $route = $this->buildRoute('/agent/login', 'AgentPage', 'Auth', 'loginAction');
        $routeCollection->add(static::ROUTE_LOGIN, $route);

        return $routeCollection;
    }

    /**
     * @uses \SprykerShop\Yves\AgentPage\Controller\AgentController::indexAction()
     *
     * @param \SprykerShop\Yves\Router\Route\RouteCollection $routeCollection
     *
     * @return \SprykerShop\Yves\Router\Route\RouteCollection
     */
    protected function addOverviewRoute(RouteCollection $routeCollection): RouteCollection
    {
        $route = $this->buildRoute('/agent/overview', 'AgentPage', 'Agent', 'indexAction');
        $routeCollection->add(static::ROUTE_AGENT_OVERVIEW, $route);

        return $routeCollection;
    }
}
