<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\VolumePriceProductWidget;

use Spryker\Yves\Kernel\AbstractBundleDependencyProvider;
use Spryker\Yves\Kernel\Container;
use SprykerShop\Yves\VolumePriceProductWidget\Dependency\Client\VolumePriceProductWidgetToCurrencyClientBridge;
use SprykerShop\Yves\VolumePriceProductWidget\Dependency\Client\VolumePriceProductWidgetToPriceClientBridge;
use SprykerShop\Yves\VolumePriceProductWidget\Dependency\Client\VolumePriceProductWidgetToPriceProductStorageClientBridge;

class VolumePriceProductWidgetDependencyProvider extends AbstractBundleDependencyProvider
{
    public const CLIENT_PRODUCT_STORAGE = 'CLIENT_PRODUCT_STORAGE';
    public const CLIENT_PRICE = 'CLIENT_PRICE';
    public const CLIENT_CURRENCY = 'CLIENT_CURRENCY';

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    public function provideDependencies(Container $container): Container
    {
        $container = $this->addProductStorageClient($container);
        $container = $this->addPriceClient($container);
        $container = $this->addCurrencyClient($container);

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addProductStorageClient(Container $container): Container
    {
        $container[static::CLIENT_PRODUCT_STORAGE] = function (Container $container) {
            return new VolumePriceProductWidgetToPriceProductStorageClientBridge(
                $container->getLocator()->priceProductStorage()->client()
            );
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addPriceClient(Container $container): Container
    {
        $container[static::CLIENT_PRICE] = function (Container $container) {
            return new VolumePriceProductWidgetToPriceClientBridge(
                $container->getLocator()->price()->client()
            );
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addCurrencyClient(Container $container): Container
    {
        $container[static::CLIENT_CURRENCY] = function (Container $container) {
            return new VolumePriceProductWidgetToCurrencyClientBridge(
                $container->getLocator()->currency()->client()
            );
        };

        return $container;
    }
}
