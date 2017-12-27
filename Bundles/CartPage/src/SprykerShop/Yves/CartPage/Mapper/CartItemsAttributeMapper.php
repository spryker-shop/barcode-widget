<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\CartPage\Mapper;

use ArrayObject;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\ProductViewTransfer;
use Generated\Shared\Transfer\StorageAttributeMapTransfer;
use Generated\Shared\Transfer\StorageAvailabilityTransfer;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Spryker\Shared\CartVariant\CartVariantConstants;
use SprykerShop\Yves\CartPage\Dependency\Client\CartPageToProductStorageClientInterface;

class CartItemsAttributeMapper implements CartItemsMapperInterface
{
    const CONCRETE_PRODUCTS_AVAILABILITY = 'concrete_products_availability';
    const CONCRETE_PRODUCT_AVAILABLE_ITEMS = 'concrete_product_available_items';
    const PRODUCT_CONCRETE_IDS = 'product_concrete_ids';

    /**
     * @var CartPageToProductStorageClientInterface
     */
    protected $productStorageClient;

    /**
     * @var CartItemsMapperInterface
     */
    protected $cartItemsAvailabilityMapper;

    /**
     * CartItemsAttributeMapper constructor.
     *
     * @param CartPageToProductStorageClientInterface $productStorageClient
     * @param CartItemsMapperInterface $cartItemsAvailabilityMapper
     */
    public function __construct(CartPageToProductStorageClientInterface $productStorageClient, CartItemsMapperInterface $cartItemsAvailabilityMapper)
    {
        $this->productStorageClient = $productStorageClient;
        $this->cartItemsAvailabilityMapper = $cartItemsAvailabilityMapper;
    }

    /**
     * @param ArrayObject $items
     * @param string $localeName
     *
     * @return array
     */
    public function buildMap(ArrayObject $items, $localeName)
    {
        $itemsAvailabilityMap = $this->cartItemsAvailabilityMapper->buildMap($items, $localeName);
        $availableItemsSkus = $this->getAvailableItemsSku($itemsAvailabilityMap);

        $attributes = [];

        foreach ($items as $item) {
            $productData = $this->getAttributesMapByProductAbstract($item, $localeName);
            $attributes[$item->getSku()] = $this->getAttributesWithAvailability(
                $item,
                $productData['attribute_map'],
                $availableItemsSkus
            );
        }

        return $attributes;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $item
     * @param array $attributeMap
     * @param array $availableItemsSkus
     *
     * @return array
     */
    protected function getAttributesWithAvailability(ItemTransfer $item, array $attributeMap, array $availableItemsSkus)
    {
        $availableConcreteProductsSku = $this->getAvailableConcreteProductsSku($attributeMap);

        $productVariants = [];

        $attributeMapIterator = $this->createAttributeIterator($attributeMap);

        foreach ($attributeMapIterator as $attribute => $productConcreteId) {
            if ($attributeMapIterator->callHasChildren() === true) {
                continue;
            }

            $variantNameValue = $this->getParentNode($attributeMapIterator);
            list($variantName, $variantValue) = explode(':', $variantNameValue);

            if ($this->isVariantNotSet($variantName, $productVariants, $variantValue)) {
                $productVariants[$variantName][$variantValue][CartVariantConstants::AVAILABLE] = false;
                $productVariants[$variantName][$variantValue][CartVariantConstants::SELECTED] = false;
            }

            if ($this->isItemSkuAvailable($availableItemsSkus, $availableConcreteProductsSku, $productConcreteId)) {
                $productVariants[$variantName][$variantValue][CartVariantConstants::AVAILABLE] = true;
            }
            if ($productConcreteId === $item->getId()) {
                $productVariants[$variantName][$variantValue][CartVariantConstants::SELECTED] = true;
            }
        }

        return $productVariants;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $item
     * @param string $localeName
     *
     * @return array
     */
    protected function getAttributesMapByProductAbstract(ItemTransfer $item, $localeName)
    {
        return $this->productStorageClient
            ->getProductAbstractStorageData($item->getIdProductAbstract(), $localeName);
    }

    /**
     * @param array $itemsAvailabilityMap
     *
     * @return array
     */
    protected function getAvailableItemsSku(array $itemsAvailabilityMap)
    {
        $availableItemsSku = [];
        foreach ($itemsAvailabilityMap as $sku => $availability) {
            if ($availability[StorageAvailabilityTransfer::CONCRETE_PRODUCT_AVAILABLE_ITEMS]) {
                $availableItemsSku[] = $sku;
            }
        }
        return $availableItemsSku;
    }

    /**
     * @param array $attributeMap
     *
     * @return array
     */
    protected function getAvailableConcreteProductsSku(array $attributeMap)
    {
        $productConcreteSkus = [];
        if (array_key_exists(static::PRODUCT_CONCRETE_IDS, $attributeMap)) {
            $productConcreteIds = $attributeMap[static::PRODUCT_CONCRETE_IDS];
            $productConcreteSkus = array_flip($productConcreteIds);
        }

        return $productConcreteSkus;
    }

    /**
     * @param array $attributeMap
     *
     * @return \RecursiveIteratorIterator
     */
    protected function createAttributeIterator(array $attributeMap)
    {
        $attributeMapIterator = new RecursiveIteratorIterator(
            new RecursiveArrayIterator($attributeMap['attribute_variants']),
            RecursiveIteratorIterator::SELF_FIRST
        );
        return $attributeMapIterator;
    }

    /**
     * @param string $variantName
     * @param array $productVariants
     * @param string $variantValue
     *
     * @return bool
     */
    protected function isVariantNotSet($variantName, array $productVariants, $variantValue)
    {
        return array_key_exists($variantName, $productVariants) === false || array_key_exists(
            $variantValue,
            $productVariants[$variantName]
        ) === false;
    }

    /**
     * @param array $availableItemsSkus
     * @param array $availableConcreteProductsSku
     * @param int $productConcreteId
     *
     * @return bool
     */
    protected function isItemSkuAvailable(array $availableItemsSkus, array $availableConcreteProductsSku, $productConcreteId)
    {
        return in_array($availableConcreteProductsSku[$productConcreteId], $availableItemsSkus, true);
    }

    /**
     * @param \RecursiveIteratorIterator $attributeMapIterator
     *
     * @return \RecursiveIterator
     */
    protected function getParentNode(RecursiveIteratorIterator $attributeMapIterator)
    {
        return $attributeMapIterator->getSubIterator($attributeMapIterator->getDepth() - 1)->key();
    }
}