<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\MultiCartWidget;

use Spryker\Yves\Kernel\AbstractFactory;
use SprykerShop\Yves\MultiCartWidget\Dependency\Client\MultiCartWidgetToMultiCartClientInterface;

class MultiCartWidgetFactory extends AbstractFactory
{
    /**
     * @return \SprykerShop\Yves\MultiCartWidget\Dependency\Client\MultiCartWidgetToMultiCartClientInterface
     */
    public function getMultiCartClient(): MultiCartWidgetToMultiCartClientInterface
    {
        return $this->getProvidedDependency(MultiCartWidgetDependencyProvider::CLIENT_MULTI_CART);
    }

    /**
     * @return array
     */
    public function getViewExtendWidgetPlugins(): array
    {
        return $this->getProvidedDependency(MultiCartWidgetDependencyProvider::PLUGINS_VIEW_EXTEND);
    }
}