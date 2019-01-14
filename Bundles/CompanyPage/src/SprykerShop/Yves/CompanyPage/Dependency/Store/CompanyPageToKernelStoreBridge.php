<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\CompanyPage\Dependency\Store;

class CompanyPageToKernelStoreBridge implements CompanyPageToKernelStoreInterface
{
    /**
     * @var \Spryker\Shared\Kernel\Store
     */
    protected $store;

    /**
     * @param \Spryker\Shared\Kernel\Store $store
     */
    public function __construct($store)
    {
        $this->store = $store;
    }

    /**
     * @return array
     */
    public function getCountries()
    {
        return $this->store->getCountries();
    }

    /**
     * @return string
     */
    public function getCurrentLocale()
    {
        return $this->store->getCurrentLocale();
    }

    /**
     * @return string[]
     */
    public function getCurrencyIsoCodes()
    {
        return $this->store->getCurrencyIsoCodes();
    }
}
