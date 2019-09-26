<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShopTest\Yves\Router\Plugin;

use Codeception\Test\Unit;
use SprykerShop\Shared\Router\RouterConstants;
use SprykerShop\Yves\Router\Plugin\RouteManipulator\LanguageDefaultRouteManipulatorPlugin;
use SprykerShop\Yves\Router\Plugin\Router\YvesRouterPlugin;
use SprykerShop\Yves\Router\Plugin\RouterEnhancer\LanguagePrefixRouterEnhancerPlugin;
use SprykerShopTest\Yves\Router\Plugin\Fixtures\RouteProviderPlugin;
use Symfony\Component\Routing\RequestContext;

/**
 * Auto-generated group annotations
 * @group SprykerTest
 * @group Yves
 * @group Router * @group Plugin
 * @group YvesRouterPluginWithLanguagePrefixRouterEnhancerTest
 * Add your own group annotations below this line
 */
class YvesRouterPluginWithLanguagePrefixRouterEnhancerTest extends Unit
{
    /**
     * @var \SprykerShopTest\Yves\Router\RouterYvesTester
     */
    protected $tester;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->tester->mockEnvironmentConfig(RouterConstants::ROUTER_CACHE_ENABLED, false);

        $this->tester->mockFactoryMethod('getRouteProviderPlugins', [
            new RouteProviderPlugin(),
        ]);

        $this->tester->mockFactoryMethod('getRouteManipulatorPlugins', [
            new LanguageDefaultRouteManipulatorPlugin(),
        ]);

        $this->tester->mockFactoryMethod('getRouterEnhancerPlugins', [
            new LanguagePrefixRouterEnhancerPlugin(),
        ]);
    }

    /**
     * @dataProvider matcherDataProvider
     *
     * @param string $url
     * @param string $routeName
     * @param string $language
     *
     * @return void
     */
    public function testMatchReturnsParameterWithLanguage(string $url, string $routeName, string $language): void
    {
        $routerPlugin = new YvesRouterPlugin();
        $routerPlugin->setFactory($this->tester->getFactory());

        $router = $routerPlugin->getRouter();

        $parameters = $router->match($url);

        $this->assertSame($routeName, $parameters['_route']);
        $this->assertSame($language, $parameters['language']);
    }

    /**
     * @dataProvider generatorDataProvider
     *
     * @param string $url
     * @param string $routeName
     * @param string $language
     *
     * @return void
     */
    public function testGenerateReturnsUrlWithLanguageWhenLanguageIsInContext(string $url, string $routeName, string $language): void
    {
        $routerPlugin = new YvesRouterPlugin();
        $routerPlugin->setFactory($this->tester->getFactory());

        $requestContext = new RequestContext();
        $requestContext->setParameter('language', $language);

        $router = $routerPlugin->getRouter();
        $router->setContext($requestContext);

        $generatedUrl = $router->generate($routeName);

        $this->assertSame($url, $generatedUrl);
    }

    /**
     * @dataProvider generatorWithoutLanguageAndStoreDataProvider
     *
     * @param string $url
     * @param string $routeName
     *
     * @return void
     */
    public function testGenerateReturnsUrlWithoutLanguageWhenLanguageIsNotInContext(string $url, string $routeName): void
    {
        $routerPlugin = new YvesRouterPlugin();
        $routerPlugin->setFactory($this->tester->getFactory());

        $router = $routerPlugin->getRouter();

        $generatedUrl = $router->generate($routeName);

        $this->assertSame($url, $generatedUrl);
    }

    /**
     * @return string[][]
     */
    public function matcherDataProvider(): array
    {
        return [
            ['/', 'home', 'en'],
            ['/de', 'home', 'de'],
            ['/foo', 'foo', 'en'],
            ['/de/foo', 'foo', 'de'],
        ];
    }

    /**
     * @return string[][]
     */
    public function generatorDataProvider(): array
    {
        return [
            ['/en', 'home', 'en'],
            ['/de', 'home', 'de'],
            ['/en/foo', 'foo', 'en'],
            ['/de/foo', 'foo', 'de'],
        ];
    }

    /**
     * @return string[][]
     */
    public function generatorWithoutLanguageAndStoreDataProvider(): array
    {
        return [
            ['/', 'home'],
            ['/foo', 'foo'],
        ];
    }
}
