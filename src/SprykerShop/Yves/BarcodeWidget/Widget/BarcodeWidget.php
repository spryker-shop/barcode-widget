<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\BarcodeWidget\Widget;

use Generated\Shared\Transfer\BarcodeResponseTransfer;
use Spryker\Yves\Kernel\Widget\AbstractWidget;

/**
 * @method \SprykerShop\Yves\BarcodeWidget\BarcodeWidgetFactory getFactory()
 */
class BarcodeWidget extends AbstractWidget
{
    /**
     * @var string
     */
    protected const PARAMETER_CODE = 'code';

    /**
     * @var string
     */
    protected const PARAMETER_ENCODING = 'encoding';

    public function __construct(string $generationText, ?string $barcodeGeneratorPlugin = null)
    {
        $barcodeResponseTransfer = $this->getFactory()
            ->getBarcodeService()
            ->generateBarcode($generationText, $barcodeGeneratorPlugin);

        $this->addCodeParameter($barcodeResponseTransfer);
        $this->addEncodingParameter($barcodeResponseTransfer);
    }

    public static function getName(): string
    {
        return 'BarcodeWidget';
    }

    public static function getTemplate(): string
    {
        return '@BarcodeWidget/views/barcode/barcode.twig';
    }

    protected function addCodeParameter(BarcodeResponseTransfer $barcodeResponseTransfer): void
    {
        $this->addParameter(static::PARAMETER_CODE, $barcodeResponseTransfer->getCode());
    }

    protected function addEncodingParameter(BarcodeResponseTransfer $barcodeResponseTransfer): void
    {
        $this->addParameter(static::PARAMETER_ENCODING, $barcodeResponseTransfer->getEncoding());
    }
}
