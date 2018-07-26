<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\PriceProductVolumeWidget\Dependency\Client;

interface PriceProductVolumeWidgetToPriceClientInterface
{
    /**
     * @return string
     */
    public function getCurrentPriceMode();
}
