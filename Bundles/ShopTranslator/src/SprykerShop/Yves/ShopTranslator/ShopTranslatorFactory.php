<?php
/**
 * Copyright © 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\ShopTranslator;

use Spryker\Yves\Kernel\AbstractFactory;
use SprykerShop\Yves\ShopTranslator\Dependency\Client\ShopTranslatorToGlossaryClientInterface;
use SprykerShop\Yves\ShopTranslator\Model\TwigTranslator;

class ShopTranslatorFactory extends AbstractFactory
{
    /**
     * @param string $locale
     *
     * @return \SprykerShop\Yves\ShopTranslator\Model\TwigTranslator
     */
    public function createTwigTranslator($locale)
    {
        return new TwigTranslator($this->getGlossaryClient(), $locale);
    }

    /**
     * @return \SprykerShop\Yves\ShopTranslator\Dependency\Client\ShopTranslatorToGlossaryClientInterface
     */
    public function getGlossaryClient(): ShopTranslatorToGlossaryClientInterface
    {
        return $this->getProvidedDependency(ShopTranslatorDependencyProvider::CLIENT_GLOSSARY);
    }
}
