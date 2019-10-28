<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\MerchantProfilePage\Dependency\Client;

use Generated\Shared\Transfer\MerchantProfileViewTransfer;

interface MerchantProfilePageToMerchantProfileStorageClientInterface
{
    /**
     * @param array $data
     *
     * @return \Generated\Shared\Transfer\MerchantProfileViewTransfer
     */
    public function mapMerchantProfileStorageViewData(array $data): MerchantProfileViewTransfer;
}
