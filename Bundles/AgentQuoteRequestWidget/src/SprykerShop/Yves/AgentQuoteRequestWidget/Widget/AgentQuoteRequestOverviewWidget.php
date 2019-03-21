<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\AgentQuoteRequestWidget\Widget;

use Generated\Shared\Transfer\PaginationTransfer;
use Generated\Shared\Transfer\QuoteRequestOverviewCollectionTransfer;
use Generated\Shared\Transfer\QuoteRequestOverviewFilterTransfer;
use Spryker\Yves\Kernel\Widget\AbstractWidget;

/**
 * @method \SprykerShop\Yves\AgentQuoteRequestWidget\AgentQuoteRequestWidgetFactory getFactory()
 * @method \SprykerShop\Yves\AgentQuoteRequestWidget\AgentQuoteRequestWidgetConfig getConfig()
 */
class AgentQuoteRequestOverviewWidget extends AbstractWidget
{
    protected const PAGINATION_PAGE = 1;

    protected const PARAMETER_FORM = 'form';
    protected const PARAMETER_QUOTE_REQUEST_OVERVIEW_COLLECTION = 'quoteRequestOverviewCollection';

    public function __construct()
    {
        $quoteRequestOverviewCollectionTransfer = $this->getQuoteRequestOverviewCollection();

        $this->addQuoteRequestOverviewCollectionParameter($quoteRequestOverviewCollectionTransfer);

        if ($quoteRequestOverviewCollectionTransfer->getCurrentQuoteRequest()) {
            $this->addFormParameter();
        }
    }

    /**
     * @return string
     */
    public static function getName(): string
    {
        return 'AgentQuoteRequestOverviewWidget';
    }

    /**
     * @return string
     */
    public static function getTemplate(): string
    {
        return '@AgentQuoteRequestWidget/views/agent-quote-request-overview/agent-quote-request-overview.twig';
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteRequestOverviewCollectionTransfer $quoteRequestOverviewCollectionTransfer
     *
     * @return void
     */
    protected function addQuoteRequestOverviewCollectionParameter(
        QuoteRequestOverviewCollectionTransfer $quoteRequestOverviewCollectionTransfer
    ): void {
        $this->addParameter(static::PARAMETER_QUOTE_REQUEST_OVERVIEW_COLLECTION, $quoteRequestOverviewCollectionTransfer);
    }

    /**
     * @return void
     */
    protected function addFormParameter(): void
    {
        $this->addParameter(static::PARAMETER_FORM, $this->getFactory()->getAgentQuoteRequestCartForm()->createView());
    }

    /**
     * @return \Generated\Shared\Transfer\QuoteRequestOverviewCollectionTransfer
     */
    protected function getQuoteRequestOverviewCollection(): QuoteRequestOverviewCollectionTransfer
    {
        $quoteTransfer = $this->getFactory()
            ->getCartClient()
            ->getQuote();

        $paginationTransfer = (new PaginationTransfer())
            ->setMaxPerPage($this->getConfig()->getPaginationDefaultQuoteRequestsPerPage())
            ->setPage(static::PAGINATION_PAGE);

        $quoteRequestOverviewFilterTransfer = (new QuoteRequestOverviewFilterTransfer())
            ->setQuoteRequestReference($quoteTransfer->getQuoteRequestReference())
            ->setExcludedStatuses($this->getConfig()->getExcludedStatuses())
            ->setPagination($paginationTransfer);

        return $this->getFactory()
            ->getAgentQuoteRequestClient()
            ->getQuoteRequestOverviewCollection($quoteRequestOverviewFilterTransfer);
    }
}
