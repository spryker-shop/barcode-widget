<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\CompanyPage\Form;

use Spryker\Yves\Kernel\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class CompanyRoleForm extends AbstractType
{
    public const FIELD_ID_COMPANY_ROLE = 'id_company_role';
    public const FIELD_NAME = 'name';
    public const FIELD_IS_DEFAULT = 'is_default';
    public const FIELD_FK_COMPANY = 'fk_company';

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'CompanyRoleForm';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->addIdCompanyRoleField($builder)
            ->addFkCompanyField($builder)
            ->addNameField($builder)
            ->addIsDefaultField($builder);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return \SprykerShop\Yves\CompanyPage\Form\CompanyRoleForm
     */
    protected function addIdCompanyRoleField(FormBuilderInterface $builder): self
    {
        $builder->add(static::FIELD_ID_COMPANY_ROLE, HiddenType::class, [
            'required' => false,
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return \SprykerShop\Yves\CompanyPage\Form\CompanyRoleForm
     */
    protected function addNameField(FormBuilderInterface $builder): self
    {
        $builder->add(static::FIELD_NAME, TextType::class, [
            'label' => 'company.account.company_role.name',
            'required' => true,
            'constraints' => [
                new NotBlank(),
            ],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return \SprykerShop\Yves\CompanyPage\Form\CompanyRoleForm
     */
    protected function addFkCompanyField(FormBuilderInterface $builder): self
    {
        $builder->add(static::FIELD_FK_COMPANY, HiddenType::class, [
            'required' => true,
            'constraints' => [
                new NotBlank(),
            ],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return \SprykerShop\Yves\CompanyPage\Form\CompanyRoleForm
     */
    protected function addIsDefaultField(FormBuilderInterface $builder): self
    {
        $builder->add(static::FIELD_IS_DEFAULT, CheckboxType::class, [
            'label' => 'company.account.company_role.is_default',
            'required' => false,
        ]);

        return $this;
    }
}