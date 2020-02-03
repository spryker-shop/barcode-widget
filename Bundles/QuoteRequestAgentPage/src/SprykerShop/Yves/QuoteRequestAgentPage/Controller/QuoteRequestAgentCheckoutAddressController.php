<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\QuoteRequestAgentPage\Controller;

use Generated\Shared\Transfer\QuoteRequestTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \SprykerShop\Yves\QuoteRequestAgentPage\QuoteRequestAgentPageFactory getFactory()
 */
class QuoteRequestAgentCheckoutAddressController extends QuoteRequestAgentAbstractController
{
    /**
     * @uses \SprykerShop\Yves\CheckoutPage\Plugin\Router\CheckoutPageRouteProviderPlugin::CHECKOUT_ADDRESS
     */
    protected const ROUTE_CHECKOUT_ADDRESS = 'checkout-address';

    /**
     * @uses \SprykerShop\Yves\QuoteRequestAgentPage\Plugin\Router\QuoteRequestAgentPageRouteProviderPlugin::ROUTE_QUOTE_REQUEST_AGENT_CHECKOUT_ADDRESS_CONFIRM
     */
    protected const ROUTE_QUOTE_REQUEST_AGENT_CHECKOUT_ADDRESS_CONFIRM = 'agent/quote-request/checkout-address-confirm';

    /**
     * @uses \SprykerShop\Yves\QuoteRequestAgentPage\Plugin\Router\QuoteRequestAgentPageRouteProviderPlugin::ROUTE_QUOTE_REQUEST_AGENT_CHECKOUT_ADDRESS
     */
    protected const ROUTE_QUOTE_REQUEST_AGENT_CHECKOUT_ADDRESS = 'agent/quote-request/checkout-address';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $quoteRequestReference
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function indexAction(Request $request, string $quoteRequestReference): RedirectResponse
    {
        $response = $this->executeIndexAction($request, $quoteRequestReference);

        return $response;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $quoteRequestReference
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executeIndexAction(Request $request, string $quoteRequestReference): RedirectResponse
    {
        $quoteTransfer = $this->getFactory()->getQuoteClient()->getQuote();

        if ($quoteTransfer->getQuoteRequestReference() && ($quoteTransfer->getQuoteRequestReference() !== $quoteRequestReference)) {
            return $this->redirectResponseInternal(static::ROUTE_QUOTE_REQUEST_AGENT_CHECKOUT_ADDRESS_CONFIRM, [
                static::PARAM_QUOTE_REQUEST_REFERENCE => $quoteRequestReference,
            ]);
        }

        $quoteRequestTransfer = $this->getQuoteRequestByReference($quoteRequestReference);

        return $this->impersonateCompanyUser($quoteRequestTransfer, $quoteTransfer);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $quoteRequestReference
     *
     * @return \Spryker\Yves\Kernel\View\View|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function confirmAction(Request $request, string $quoteRequestReference)
    {
        $response = $this->executeConfirmAction($request, $quoteRequestReference);

        if (!is_array($response)) {
            return $response;
        }

        return $this->view($response, [], '@QuoteRequestAgentPage/views/quote-request-checkout-address-confirm/quote-request-checkout-address-confirm.twig');
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $quoteRequestReference
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executeConfirmAction(Request $request, string $quoteRequestReference)
    {
        $quoteTransfer = $this->getFactory()->getQuoteClient()->getQuote();

        if ($quoteTransfer->getQuoteRequestReference() === $quoteRequestReference) {
            return $this->redirectResponseInternal(static::ROUTE_QUOTE_REQUEST_AGENT_CHECKOUT_ADDRESS, [
                static::PARAM_QUOTE_REQUEST_REFERENCE => $quoteRequestReference,
            ]);
        }

        $quoteRequestTransfer = $this->getQuoteRequestByReference($quoteRequestReference);
        $quoteRequestAgentEditAddressConfirmForm = $this->getFactory()
            ->getQuoteRequestAgentEditAddressConfirmForm($quoteRequestTransfer)
            ->handleRequest($request);

        if ($quoteRequestAgentEditAddressConfirmForm->isSubmitted()) {
            $this->impersonateCompanyUser($quoteRequestAgentEditAddressConfirmForm->getData(), $quoteTransfer);
        }

        return [
            'quoteRequestEditAddressConfirmForm' => $quoteRequestAgentEditAddressConfirmForm->createView(),
            'quoteRequestReference' => $quoteTransfer->getQuoteRequestReference(),
        ];
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteRequestTransfer $quoteRequestTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function impersonateCompanyUser(QuoteRequestTransfer $quoteRequestTransfer, QuoteTransfer $quoteTransfer)
    {
        $companyUserTransfer = $this->getFactory()->getCompanyUserClient()->findCompanyUser();
        $impersonationRedirectParams = $this->getFactory()
            ->createCompanyUserImpersonator()
            ->getImpersonationCompanyUserExitParams($quoteRequestTransfer, $companyUserTransfer);

        if ($impersonationRedirectParams) {
            return $this->redirectResponseInternal(static::ROUTE_QUOTE_REQUEST_AGENT_CHECKOUT_ADDRESS, $impersonationRedirectParams);
        }

        $this->getFactory()
            ->createCompanyUserImpersonator()
            ->convertQuoteRequestToQuote($quoteRequestTransfer, $quoteTransfer);

        $impersonationRedirectParams = $this->getFactory()
            ->createCompanyUserImpersonator()
            ->getImpersonationCompanyUserEmailParams($quoteRequestTransfer, $companyUserTransfer);

        return $this->redirectResponseInternal(static::ROUTE_CHECKOUT_ADDRESS, $impersonationRedirectParams);
    }
}
