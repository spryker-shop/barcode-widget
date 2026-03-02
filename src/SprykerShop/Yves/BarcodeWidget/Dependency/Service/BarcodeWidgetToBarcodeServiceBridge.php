<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\BarcodeWidget\Dependency\Service;

use Generated\Shared\Transfer\BarcodeResponseTransfer;

class BarcodeWidgetToBarcodeServiceBridge implements BarcodeWidgetToBarcodeServiceInterface
{
    /**
     * @var \Spryker\Service\Barcode\BarcodeServiceInterface
     */
    protected $barcodeService;

    /**
     * @param \Spryker\Service\Barcode\BarcodeServiceInterface $barcodeService
     */
    public function __construct($barcodeService)
    {
        $this->barcodeService = $barcodeService;
    }

    public function generateBarcode(string $text, ?string $generatorPlugin = null): BarcodeResponseTransfer
    {
        return $this->barcodeService->generateBarcode($text, $generatorPlugin);
    }
}
