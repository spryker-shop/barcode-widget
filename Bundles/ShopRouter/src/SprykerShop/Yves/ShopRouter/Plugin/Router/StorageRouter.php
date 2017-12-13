<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerShop\Yves\ShopRouter\Plugin\Router;

use Spryker\Yves\Application\Routing\AbstractRouter;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * @method \SprykerShop\Yves\ShopRouter\ShopRouterFactory getFactory()
 */
class StorageRouter extends AbstractRouter
{
    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     */
    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH)
    {
        $urlMatcher = $this->getFactory()->getUrlMatcher();
        $localeName = $this->getApplication()['locale'];

        if (!$urlMatcher->matchUrl($name, $localeName)) {
            $name = $this->getDefaultLocalePrefix() . $name;

            if (!$urlMatcher->matchUrl($name, $localeName)) {
                throw new RouteNotFoundException();
            }
        }

        $requestParameters = $this->getRequest()->query->all();

        $mergedParameters = $this
            ->getFactory()
            ->createParameterMerger()
            ->mergeParameters($requestParameters, $parameters);

        $pathInfo = $this
            ->getFactory()
            ->createUrlMapper()
            ->generateUrlFromParameters($mergedParameters);

        $pathInfo = $name . $pathInfo;

        return $this->getUrlOrPathForType($pathInfo, $referenceType);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\Routing\Exception\ResourceNotFoundException
     */
    public function match($pathinfo)
    {
        $defaultLocalePrefix = $this->getDefaultLocalePrefix();

        if ($defaultLocalePrefix === $pathinfo || $defaultLocalePrefix . '/' === $pathinfo) {
            throw new ResourceNotFoundException();
        }

        if ($pathinfo !== '/') {
            $urlMatcher = $this->getFactory()->getUrlMatcher();
            $localeName = $this->getApplication()['locale'];

            $urlDetails = $urlMatcher->matchUrl($pathinfo, $localeName);

            if (!$urlDetails) {
                $urlDetails = $urlMatcher->matchUrl($defaultLocalePrefix . $pathinfo, $localeName);
            }

            if ($urlDetails) {
                $resourceCreatorResult = $this->getFactory()
                    ->createResourceCreatorHandler()
                    ->create($urlDetails['type'], $urlDetails['data']);

                if ($resourceCreatorResult) {
                    return $resourceCreatorResult;
                }
            }
        }

        throw new ResourceNotFoundException();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    protected function getRequest()
    {
        $application = $this->getApplication();
        $request = ($application['request_stack']) ? $application['request_stack']->getCurrentRequest() : $application['request'];

        return $request;
    }

    /**
     * @return \Silex\Application
     */
    protected function getApplication()
    {
        return $this->getFactory()->getApplication();
    }

    /**
     * @return string
     */
    protected function getDefaultLocalePrefix()
    {
        return '/' . mb_substr($this->getApplication()['locale'], 0, 2);
    }
}
