<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\OrderCustomReferenceWidget\Validator;

interface OrderCustomReferenceValidatorInterface
{
    /**
     * @param string|null $orderCustomReference
     *
     * @return bool
     */
    public function isOrderCustomReferenceLengthValid(?string $orderCustomReference): bool;
}
