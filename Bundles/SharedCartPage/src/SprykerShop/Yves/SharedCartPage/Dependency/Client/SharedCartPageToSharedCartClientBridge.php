<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\SharedCartPage\Dependency\Client;

use Generated\Shared\Transfer\QuotePermissionGroupCriteriaFilterTransfer;
use Generated\Shared\Transfer\QuotePermissionGroupResponseTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\ShareCartRequestTransfer;

class SharedCartPageToSharedCartClientBridge implements SharedCartPageToSharedCartClientInterface
{
    /**
     * @var \Spryker\Client\SharedCart\SharedCartClientInterface
     */
    protected $sharedCartClient;

    /**
     * @param \Spryker\Client\SharedCart\SharedCartClientInterface $sharedCartClient
     */
    public function __construct($sharedCartClient)
    {
        $this->sharedCartClient = $sharedCartClient;
    }

    /**
     * @param \Generated\Shared\Transfer\QuotePermissionGroupCriteriaFilterTransfer $criteriaFilterTransfer
     *
     * @return \Generated\Shared\Transfer\QuotePermissionGroupResponseTransfer
     */
    public function getQuotePermissionGroupList(QuotePermissionGroupCriteriaFilterTransfer $criteriaFilterTransfer): QuotePermissionGroupResponseTransfer
    {
        return $this->sharedCartClient->getQuotePermissionGroupList($criteriaFilterTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\ShareCartRequestTransfer $shareCartRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function addShareCart(ShareCartRequestTransfer $shareCartRequestTransfer): QuoteResponseTransfer
    {
        return $this->sharedCartClient->addShareCart($shareCartRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\ShareCartRequestTransfer $shareCartRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function removeShareCart(ShareCartRequestTransfer $shareCartRequestTransfer): QuoteResponseTransfer
    {
        return $this->sharedCartClient->removeShareCart($shareCartRequestTransfer);
    }
}