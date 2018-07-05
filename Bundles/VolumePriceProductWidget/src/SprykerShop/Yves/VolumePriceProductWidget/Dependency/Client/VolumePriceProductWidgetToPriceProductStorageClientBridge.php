<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\VolumePriceProductWidget\Dependency\Client;

class VolumePriceProductWidgetToPriceProductStorageClientBridge implements VolumePriceProductWidgetToPriceProductStorageClientInterface
{
    /**
     * @var \Spryker\Client\PriceProductStorage\PriceProductStorageClientInterface
     */
    protected $priceProductStorageClient;

    /**
     * @param \Spryker\Client\PriceProductStorage\PriceProductStorageClientInterface $priceProductStorageClient
     */
    public function __construct($priceProductStorageClient)
    {
        $this->priceProductStorageClient = $priceProductStorageClient;
    }

    /**
     * @param int $idProductAbstract
     *
     * @return \Generated\Shared\Transfer\PriceProductTransfer[]|null
     */
    public function getPriceProductAbstractTransfers(int $idProductAbstract): array
    {
        return $this->priceProductStorageClient->getPriceProductAbstractTransfers($idProductAbstract);
    }

    /**
     * @param int $idProductConcrete
     *
     * @return \Generated\Shared\Transfer\PriceProductTransfer[]|null
     */
    public function getPriceProductConcreteTransfers(int $idProductConcrete): array
    {
        return $this->priceProductStorageClient->getPriceProductConcreteTransfers($idProductConcrete);
    }
}
