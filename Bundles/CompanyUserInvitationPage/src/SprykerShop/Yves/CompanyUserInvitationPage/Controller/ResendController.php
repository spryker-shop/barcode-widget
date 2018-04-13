<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\CompanyUserInvitationPage\Controller;

use Generated\Shared\Transfer\CompanyUserInvitationTransfer;
use SprykerShop\Shared\CompanyUserInvitationPage\CompanyUserInvitationPageConstants;
use SprykerShop\Yves\CompanyUserInvitationPage\Plugin\Provider\CompanyUserInvitationPageControllerProvider;
use SprykerShop\Yves\ShopApplication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \SprykerShop\Yves\CompanyUserInvitationPage\CompanyUserInvitationPageFactory getFactory()
 */
class ResendController extends AbstractController
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array|\Spryker\Yves\Kernel\View\View
     */
    public function indexAction(Request $request)
    {
        return $this->view([
            'id' => (int)$request->get(CompanyUserInvitationPageConstants::INVITATION_ID),
        ], [], '@CompanyUserInvitationPage/views/invitation-resend/invitation-resend.twig');
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function confirmAction(Request $request): RedirectResponse
    {
        $invitationId = (int)$request->get(CompanyUserInvitationPageConstants::INVITATION_ID);
        $companyUserInvitationTransfer = (new CompanyUserInvitationTransfer())
            ->setIdCompanyUserInvitation($invitationId);

        $companyUserInvitationSendResultTransfer = $this->getFactory()
            ->getCompanyUserInvitationClient()
            ->sendCompanyUserInvitation($companyUserInvitationTransfer);

        if ($companyUserInvitationSendResultTransfer->getSuccess()) {
            $this->addSuccessMessage('company.user.invitation.resent.success.message');
            return $this->redirectResponseInternal(CompanyUserInvitationPageControllerProvider::ROUTE_OVERVIEW);
        }

        $this->addErrorMessage('company.user.invitation.resent.error.message');
        return $this->redirectResponseInternal(CompanyUserInvitationPageControllerProvider::ROUTE_OVERVIEW);
    }
}
