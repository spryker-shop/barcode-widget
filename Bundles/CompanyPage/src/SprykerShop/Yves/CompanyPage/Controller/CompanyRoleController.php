<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\CompanyPage\Controller;

use ArrayObject;
use Generated\Shared\Transfer\CompanyBusinessUnitTransfer;
use Generated\Shared\Transfer\CompanyRoleCriteriaFilterTransfer;
use Generated\Shared\Transfer\CompanyRoleResponseTransfer;
use Generated\Shared\Transfer\CompanyRoleTransfer;
use Generated\Shared\Transfer\CompanyUserCollectionTransfer;
use Generated\Shared\Transfer\PermissionTransfer;
use SprykerShop\Yves\CompanyPage\Plugin\Provider\CompanyPageControllerProvider;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class CompanyRoleController extends AbstractCompanyController
{
    public const COMPANY_ROLE_SORT_FIELD = 'id_company_role';

    protected const SUCCESS_MESSAGE_COMPANY_ROLE_DELETE = 'company.account.company_role.delete.successful';
    protected const PARAMETER_ID_COMPANY_ROLE = 'id';
    protected const ERROR_MESSAGE_DEFAULT_COMPANY_ROLE_DELETE = 'company.account.company_role.delete.error.default_role';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array|\Spryker\Yves\Kernel\View\View|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function indexAction(Request $request)
    {
        $viewData = $this->executeIndexAction($request);

        return $this->view($viewData, [], '@CompanyPage/views/role/role.twig');
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    protected function executeIndexAction(Request $request): array
    {
        $collectionTransfer = $this->createCriteriaFilterTransfer($request);
        $collectionTransfer = $this->getFactory()
            ->getCompanyRoleClient()
            ->getCompanyRoleCollection($collectionTransfer);

        return [
            'companyRoleCollection' => $collectionTransfer->getRoles(),
            'pagination' => $collectionTransfer->getPagination(),
        ];
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array|\Spryker\Yves\Kernel\View\View
     */
    public function detailsAction(Request $request)
    {
        $viewData = $this->executeDetailsAction($request);

        return $this->view($viewData, [], '@CompanyPage/views/role-detail/role-detail.twig');
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    protected function executeDetailsAction(Request $request): array
    {
        $idCompanyRole = $request->query->getInt('id');
        $companyRoleTransfer = new CompanyRoleTransfer();
        $companyRoleTransfer->setIdCompanyRole($idCompanyRole);
        $companyRoleTransfer = $this->getFactory()
            ->getCompanyRoleClient()
            ->getCompanyRoleById($companyRoleTransfer);

        $companyRolePermissions = $this->getFactory()
            ->getCompanyRoleClient()
            ->findCompanyRolePermissions($companyRoleTransfer);

        $companyUserCollection = $this->prepareCompanyUsers($companyRoleTransfer->getCompanyUserCollection());

        return [
            'companyRole' => $companyRoleTransfer,
            'permissions' => $companyRolePermissions->getPermissions(),
            'companyUserCollection' => $companyUserCollection->getCompanyUsers(),
        ];
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request): RedirectResponse
    {
        $idCompanyRole = $request->query->getInt('id');
        $companyRoleTransfer = new CompanyRoleTransfer();
        $companyRoleTransfer->setIdCompanyRole($idCompanyRole);

        $companyRoleResponseTransfer = $this->getFactory()->getCompanyRoleClient()->deleteCompanyRole($companyRoleTransfer);

        if ($companyRoleResponseTransfer->getIsSuccessful()) {
            $this->addSuccessMessage(static::SUCCESS_MESSAGE_COMPANY_ROLE_DELETE);
        }

        $this->processResponseMessages($companyRoleResponseTransfer);

        return $this->redirectResponseInternal(CompanyPageControllerProvider::ROUTE_COMPANY_ROLE);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Spryker\Yves\Kernel\View\View|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function confirmDeleteAction(Request $request)
    {
        $response = $this->executeConfirmDeleteAction($request);

        if (!is_array($response)) {
            return $response;
        }

        return $this->view($response, [], '@CompanyPage/views/role-delete/role-delete.twig');
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executeConfirmDeleteAction(Request $request)
    {
        $idCompanyRole = $request->query->getInt(static::PARAMETER_ID_COMPANY_ROLE);

        $companyRoleTransfer = (new CompanyRoleTransfer())
            ->setIdCompanyRole($idCompanyRole);

        $companyRoleTransfer = $this->getFactory()
            ->getCompanyRoleClient()
            ->getCompanyRoleById($companyRoleTransfer);

        if ($companyRoleTransfer->getIsDefault()) {
            $this->addErrorMessage(static::ERROR_MESSAGE_DEFAULT_COMPANY_ROLE_DELETE);

            return $this->redirectResponseInternal(CompanyPageControllerProvider::ROUTE_COMPANY_ROLE);
        }

        return [
            'idCompanyRole' => $idCompanyRole,
            'role' => $companyRoleTransfer,
        ];
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Spryker\Yves\Kernel\View\View|\Spryker\Yves\Kernel\View\View|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function createAction(Request $request)
    {
        $response = $this->executeCreateAction($request);

        if (!is_array($response)) {
            return $response;
        }

        return $this->view($response, [], '@CompanyPage/views/role-create/role-create.twig');
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executeCreateAction(Request $request)
    {
        $dataProvider = $this->getFactory()
            ->createCompanyPageFormFactory()
            ->createCompanyRoleDataProvider();

        $companyRoleForm = $this
            ->getFactory()
            ->createCompanyPageFormFactory()
            ->getCompanyRoleForm()
            ->handleRequest($request);

        if ($companyRoleForm->isSubmitted() === false) {
            $idCompany = $this->getCompanyUser()->getFkCompany();
            $companyRoleForm->setData($dataProvider->getData($idCompany));
        }

        if ($companyRoleForm->isSubmitted() && $companyRoleForm->isValid()) {
            $companyRoleResponseTransfer = $this->createCompanyRole($companyRoleForm->getData());
            if ($companyRoleResponseTransfer->getIsSuccessful()) {
                return $this->redirectResponseInternal(CompanyPageControllerProvider::ROUTE_COMPANY_ROLE);
            }

            $this->processResponseMessages($companyRoleResponseTransfer);
        }

        return [
            'companyRoleForm' => $companyRoleForm->createView(),
        ];
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array|\Spryker\Yves\Kernel\View\View|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function updateAction(Request $request)
    {
        $response = $this->executeUpdateAction($request);

        if (!is_array($response)) {
            return $response;
        }

        return $this->view($response, [], '@CompanyPage/views/role-update/role-update.twig');
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executeUpdateAction(Request $request)
    {
        $dataProvider = $this->getFactory()
            ->createCompanyPageFormFactory()
            ->createCompanyRoleDataProvider();

        $companyRoleForm = $this
            ->getFactory()
            ->createCompanyPageFormFactory()
            ->getCompanyRoleForm()
            ->handleRequest($request);

        $idCompanyRole = $request->query->getInt(static::PARAMETER_ID_COMPANY_ROLE);

        if ($companyRoleForm->isSubmitted() === false) {
            $idCompany = $this->getCompanyUser()->getFkCompany();
            $companyRoleForm->setData($dataProvider->getData($idCompany, $idCompanyRole));
        }

        if ($companyRoleForm->isSubmitted() && $companyRoleForm->isValid()) {
            $this->updateCompanyRole($companyRoleForm->getData());

            return $this->redirectResponseInternal(CompanyPageControllerProvider::ROUTE_COMPANY_ROLE);
        }

        return [
            'companyRoleForm' => $companyRoleForm->createView(),
            'idCompanyRole' => $idCompanyRole,
            'permissions' => $this->getPermissionsList($idCompanyRole),
        ];
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Generated\Shared\Transfer\CompanyRoleCriteriaFilterTransfer
     */
    protected function createCriteriaFilterTransfer(Request $request): CompanyRoleCriteriaFilterTransfer
    {
        $criteriaFilterTransfer = new CompanyRoleCriteriaFilterTransfer();

        $criteriaFilterTransfer->setIdCompany($this->getCompanyUser()->getFkCompany());
        $criteriaFilterTransfer->setPagination($this->createPaginationTransfer($request));
        $criteriaFilterTransfer->setFilter($this->createFilterTransfer(static::COMPANY_ROLE_SORT_FIELD));

        return $criteriaFilterTransfer;
    }

    /**
     * @param array $data
     *
     * @return \Generated\Shared\Transfer\CompanyRoleResponseTransfer
     */
    protected function createCompanyRole(array $data): CompanyRoleResponseTransfer
    {
        $companyRoleTransfer = new CompanyRoleTransfer();
        $companyRoleTransfer->fromArray($data, true);

        return $this->getFactory()
            ->getCompanyRoleClient()
            ->createCompanyRole($companyRoleTransfer);
    }

    /**
     * @param array $data
     *
     * @return void
     */
    protected function updateCompanyRole(array $data): void
    {
        $companyRoleTransfer = new CompanyRoleTransfer();
        $companyRoleTransfer->fromArray($data, true);

        $this->getFactory()
            ->getCompanyRoleClient()
            ->updateCompanyRole($companyRoleTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\CompanyUserCollectionTransfer $companyUserCollection
     *
     * @return \Generated\Shared\Transfer\CompanyUserCollectionTransfer
     */
    protected function prepareCompanyUsers(CompanyUserCollectionTransfer $companyUserCollection)
    {
        foreach ($companyUserCollection->getCompanyUsers() as $companyUser) {
            if ($companyUser->getFkCompanyBusinessUnit()) {
                $companyBusinessUnitTransfer = $this->getFactory()
                    ->getCompanyBusinessUnitClient()
                    ->getCompanyBusinessUnitById(
                        (new CompanyBusinessUnitTransfer())->setIdCompanyBusinessUnit(
                            $companyUser->getFkCompanyBusinessUnit()
                        )
                    );
                $companyUser->setCompanyBusinessUnit($companyBusinessUnitTransfer);
            }
        }

        return $companyUserCollection;
    }

    /**
     * @param int $idCompanyRole
     *
     * @return array
     */
    protected function getPermissionsList(int $idCompanyRole): array
    {
        $allPermissionTransfers = $this->getFactory()
            ->getPermissionClient()
            ->findAll()
            ->getPermissions();

        $companyRoleTransfer = new CompanyRoleTransfer();
        $companyRoleTransfer->setIdCompanyRole($idCompanyRole);

        $companyRolePermissionTransfers = $this->getFactory()
            ->getCompanyRoleClient()
            ->findCompanyRolePermissions($companyRoleTransfer)
            ->getPermissions();

        return $this->preparePermissions(
            $allPermissionTransfers,
            $companyRolePermissionTransfers,
            $idCompanyRole
        );
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\PermissionTransfer[] $allPermissionTransfers
     * @param \ArrayObject|\Generated\Shared\Transfer\PermissionTransfer[] $companyRolePermissionTransfers
     * @param int $idCompanyRole
     *
     * @return array
     */
    protected function preparePermissions(
        ArrayObject $allPermissionTransfers,
        ArrayObject $companyRolePermissionTransfers,
        int $idCompanyRole
    ): array {
        $permissions = [];

        $registeredPermissionTransfers = $this->getFactory()
            ->getPermissionClient()
            ->getRegisteredPermissions()
            ->getPermissions();

        $awareConfigurationPermissionKeys = $this->getAwareConfigurationPermissionKeys($registeredPermissionTransfers);

        foreach ($allPermissionTransfers as $permissionTransfer) {
            if (in_array($permissionTransfer->getKey(), $awareConfigurationPermissionKeys)) {
                continue;
            }

            $permissions[] = $this->transformCompanyRolePermissionTransferToArray(
                $companyRolePermissionTransfers,
                $permissionTransfer,
                $idCompanyRole
            );
        }

        return $permissions;
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\PermissionTransfer[] $companyRolePermissionTransfers
     * @param \Generated\Shared\Transfer\PermissionTransfer $permissionTransfer
     * @param int $idCompanyRole
     *
     * @return array
     */
    protected function transformCompanyRolePermissionTransferToArray(
        ArrayObject $companyRolePermissionTransfers,
        PermissionTransfer $permissionTransfer,
        int $idCompanyRole
    ): array {
        $permissionAsArray = $permissionTransfer->toArray(false, true);
        $permissionAsArray[CompanyRoleTransfer::ID_COMPANY_ROLE] = null;

        foreach ($companyRolePermissionTransfers as $rolePermission) {
            if ($rolePermission->getKey() === $permissionTransfer->getKey()) {
                $permissionAsArray[CompanyRoleTransfer::ID_COMPANY_ROLE] = $idCompanyRole;
                break;
            }
        }

        return $permissionAsArray;
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\PermissionTransfer[] $registeredPermissionTransfers
     *
     * @return string[]
     */
    protected function getAwareConfigurationPermissionKeys(ArrayObject $registeredPermissionTransfers): array
    {
        $awareConfigurationPermissionKeys = [];

        foreach ($registeredPermissionTransfers as $permissionTransfer) {
            if ($permissionTransfer->getIsAwareConfiguration()) {
                $awareConfigurationPermissionKeys[] = $permissionTransfer->getKey();
            }
        }

        return $awareConfigurationPermissionKeys;
    }
}
