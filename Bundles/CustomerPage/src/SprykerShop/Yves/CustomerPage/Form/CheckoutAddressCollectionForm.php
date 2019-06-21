<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\CustomerPage\Form;

use ArrayObject;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\ShipmentTransfer;
use Spryker\Yves\Kernel\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\IsFalse;

/**
 * @method \SprykerShop\Yves\CustomerPage\CustomerPageConfig getConfig()
 * @method \SprykerShop\Yves\CustomerPage\CustomerPageFactory getFactory()
 */
class CheckoutAddressCollectionForm extends AbstractType
{
    public const FIELD_SHIPPING_ADDRESS = 'shippingAddress';
    public const FIELD_BILLING_ADDRESS = 'billingAddress';
    public const FIELD_BILLING_SAME_AS_SHIPPING = 'billingSameAsShipping';
    public const FIELD_MULTI_SHIPPING_ADDRESSES = 'multiShippingAddresses';

    public const OPTION_ADDRESS_CHOICES = 'address_choices';
    public const OPTION_COUNTRY_CHOICES = 'country_choices';
    public const OPTION_CAN_DELIVER_TO_MULTIPLE_SHIPPING_ADDRESSES = 'can_deliver_to_multiple_shipping_addresses';
    public const OPTION_IS_MULTI_SHIPMENT_ENABLED = 'is_multi_shipment_enabled';
    public const OPTION_IS_CUSTOMER_LOGGED_IN = 'is_customer_logged_in';

    public const GROUP_SHIPPING_ADDRESS = self::FIELD_SHIPPING_ADDRESS;
    public const GROUP_BILLING_ADDRESS = self::FIELD_BILLING_ADDRESS;
    public const GROUP_BILLING_SAME_AS_SHIPPING = self::FIELD_BILLING_SAME_AS_SHIPPING;

    public const VALIDATION_BILLING_SAME_AS_SHIPPING_MESSAGE = 'Billing address should be specified when shipping to multiple addresses.';

    protected const GLOSSARY_KEY_SAVE_NEW_ADDRESS = 'customer.address.save_new_address';
    protected const GLOSSARY_KEY_DELIVER_TO_MULTIPLE_ADDRESSES = 'customer.account.deliver_to_multiple_addresses';

    protected const PROPERTY_PATH_MULTI_SHIPPING_ADDRESSES = 'items';

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'addressesForm';
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        /** @var \Symfony\Component\OptionsResolver\OptionsResolver $resolver */
        $resolver->setDefaults([
            'validation_groups' => function (FormInterface $form) {
                $validationGroups = [Constraint::DEFAULT_GROUP, static::GROUP_SHIPPING_ADDRESS];

                if (!$form->get(static::FIELD_BILLING_SAME_AS_SHIPPING)->getData()) {
                    $validationGroups[] = static::GROUP_BILLING_ADDRESS;
                }

                return $validationGroups;
            },
            static::OPTION_ADDRESS_CHOICES => [],
        ]);

        $resolver->setDefined(static::OPTION_ADDRESS_CHOICES)
            ->setRequired(static::OPTION_COUNTRY_CHOICES)
            ->setRequired(static::OPTION_CAN_DELIVER_TO_MULTIPLE_SHIPPING_ADDRESSES)
            ->setRequired(static::OPTION_IS_MULTI_SHIPMENT_ENABLED)
            ->setRequired(static::OPTION_IS_CUSTOMER_LOGGED_IN);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this
            ->addShippingAddressSubForm($builder, $options)
            ->addItemShippingAddressSubForm($builder, $options)
            ->addSameAsShippingCheckboxField($builder)
            ->addBillingAddressSubForm($builder, $options);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return $this
     */
    protected function addShippingAddressSubForm(FormBuilderInterface $builder, array $options)
    {
        $options = [
            'data_class' => AddressTransfer::class,
            'required' => true,
            'mapped' => false,
            'validation_groups' => function (FormInterface $form) {
                if ($this->isIdCustomerAddressFieldNotEmpty($form)
                    || $this->isIdCompanyUnitAddressFieldNotEmpty($form)) {
                    return false;
                }

                return [static::GROUP_SHIPPING_ADDRESS];
            },
            CheckoutAddressForm::OPTION_VALIDATION_GROUP => static::GROUP_SHIPPING_ADDRESS,
            CheckoutAddressForm::OPTION_ADDRESS_CHOICES => $this->getShippingAddressChoices($options),
            CheckoutAddressForm::OPTION_COUNTRY_CHOICES => $options[static::OPTION_COUNTRY_CHOICES],
            CheckoutAddressForm::OPTION_IS_CUSTOMER_LOGGED_IN => $options[static::OPTION_IS_CUSTOMER_LOGGED_IN],
        ];

        $builder->add(static::FIELD_SHIPPING_ADDRESS, CheckoutAddressForm::class, $options);

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            $this->hydrateShippingAddressSubFormDataFromItemLevelShippingAddresses($event);
        });

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $this->mapSubmittedShippingAddressSubFormDataToItemLevelShippingAddresses($event);
        });

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormEvent $event
     *
     * @return void
     */
    protected function hydrateShippingAddressSubFormDataFromItemLevelShippingAddresses(FormEvent $event): void
    {
        $quoteTransfer = $event->getData();
        if (!($quoteTransfer instanceof QuoteTransfer)) {
            return;
        }

        $form = $event->getForm();
        $shippingAddressFrom = $form->get(static::FIELD_SHIPPING_ADDRESS);
        if ($this->isDeliverToMultipleAddressesEnabled($shippingAddressFrom)) {
            return;
        }

        $itemTransfer = current($quoteTransfer->getItems());

        if (!$itemTransfer) {
            return;
        }

        if ($itemTransfer->getShipment() === null
            || $itemTransfer->getShipment()->getShippingAddress() === null) {
            return;
        }

        $shippingAddressFrom->setData(clone $itemTransfer->getShipment()->getShippingAddress());
    }

    /**
     * @param \Symfony\Component\Form\FormEvent $event
     *
     * @return \Symfony\Component\Form\FormEvent
     */
    protected function mapSubmittedShippingAddressSubFormDataToItemLevelShippingAddresses(FormEvent $event): FormEvent
    {
        $quoteTransfer = $event->getData();
        if (!($quoteTransfer instanceof QuoteTransfer)) {
            return $event;
        }

        $form = $event->getForm();
        $shippingAddressFrom = $form->get(static::FIELD_SHIPPING_ADDRESS);
        if ($this->isDeliverToMultipleAddressesEnabled($shippingAddressFrom)) {
            return $event;
        }

        $shippingAddressTransfer = $shippingAddressFrom->getData();
        $shipmentTransfer = (new ShipmentTransfer())
            ->setShippingAddress($shippingAddressTransfer);

        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            $itemTransfer->setShipment($shipmentTransfer);
        }

        $event->setData($quoteTransfer);

        return $event;
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $form
     *
     * @return bool
     */
    protected function isDeliverToMultipleAddressesEnabled(FormInterface $form): bool
    {
        if (!$form->has(CheckoutAddressForm::FIELD_ID_CUSTOMER_ADDRESS)) {
            return false;
        }

        $idCustomerAddress = $form->get(CheckoutAddressForm::FIELD_ID_CUSTOMER_ADDRESS)->getViewData();

        return $idCustomerAddress === CheckoutAddressForm::VALUE_DELIVER_TO_MULTIPLE_ADDRESSES;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addSameAsShippingCheckboxField(FormBuilderInterface $builder)
    {
        $builder->add(
            static::FIELD_BILLING_SAME_AS_SHIPPING,
            CheckboxType::class,
            [
                'required' => false,
                'constraints' => [
                    $this->createBillingSameAsShippingConstraint(),
                ],
                'validation_groups' => function (FormInterface $form) {
                    $shippingAddressForm = $form->getParent()
                        ? $form->getParent()->get(static::FIELD_SHIPPING_ADDRESS)
                        : null;

                    if (!$shippingAddressForm) {
                        return false;
                    }

                    if (!$this->isDeliverToMultipleAddressesEnabled($shippingAddressForm)) {
                        return false;
                    }

                    return [static::GROUP_BILLING_SAME_AS_SHIPPING];
                },
            ]
        );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return $this
     */
    protected function addBillingAddressSubForm(FormBuilderInterface $builder, array $options)
    {
        $options = [
            'data_class' => AddressTransfer::class,
            'validation_groups' => function (FormInterface $form) {
                if ($form->getParent()->get(static::FIELD_BILLING_SAME_AS_SHIPPING)->getData()) {
                    return false;
                }

                if ($this->isIdCustomerAddressExistAndNotEmpty($form)
                    || $this->isIdCompanyUnitAddressFieldExistAndNotEmpty($form)) {
                    return false;
                }

                return [static::GROUP_BILLING_ADDRESS];
            },
            'required' => true,
            CheckoutAddressForm::OPTION_VALIDATION_GROUP => static::GROUP_BILLING_ADDRESS,
            CheckoutAddressForm::OPTION_ADDRESS_CHOICES => $options[static::OPTION_ADDRESS_CHOICES],
            CheckoutAddressForm::OPTION_COUNTRY_CHOICES => $options[static::OPTION_COUNTRY_CHOICES],
            CheckoutAddressForm::OPTION_IS_CUSTOMER_LOGGED_IN => $options[static::OPTION_IS_CUSTOMER_LOGGED_IN],
        ];

        $builder->add(static::FIELD_BILLING_ADDRESS, CheckoutAddressForm::class, $options);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return $this
     */
    protected function addItemShippingAddressSubForm(FormBuilderInterface $builder, array $options)
    {
        $fieldOptions = [
            'label' => false,
            'property_path' => static::PROPERTY_PATH_MULTI_SHIPPING_ADDRESSES,
            'entry_type' => CheckoutMultiShippingAddressesForm::class,
            'entry_options' => [
                'data_class' => ItemTransfer::class,
                'label' => false,
                CheckoutMultiShippingAddressesForm::OPTION_VALIDATION_GROUP => static::GROUP_SHIPPING_ADDRESS,
                CheckoutMultiShippingAddressesForm::OPTION_ADDRESS_CHOICES => $options[static::OPTION_ADDRESS_CHOICES],
                CheckoutMultiShippingAddressesForm::OPTION_COUNTRY_CHOICES => $options[static::OPTION_COUNTRY_CHOICES],
                CheckoutMultiShippingAddressesForm::OPTION_IS_CUSTOMER_LOGGED_IN => $options[static::OPTION_IS_CUSTOMER_LOGGED_IN],
            ],
        ];
        if (!$options[static::OPTION_IS_MULTI_SHIPMENT_ENABLED]) {
            $fieldOptions['data'] = new ArrayObject();
        }

        $builder->add(static::FIELD_MULTI_SHIPPING_ADDRESSES, CollectionType::class, $fieldOptions);

        return $this;
    }

    /**
     * @param array $options
     *
     * @return string[]
     */
    protected function getShippingAddressChoices(array $options): array
    {
        if (!$options[static::OPTION_CAN_DELIVER_TO_MULTIPLE_SHIPPING_ADDRESSES]) {
            return $options[static::OPTION_ADDRESS_CHOICES];
        }

        $addressChoices = $options[static::OPTION_ADDRESS_CHOICES];
        $addressChoices[CheckoutAddressForm::VALUE_DELIVER_TO_MULTIPLE_ADDRESSES] = static::GLOSSARY_KEY_DELIVER_TO_MULTIPLE_ADDRESSES;

        return $addressChoices;
    }

    /**
     * @return \Symfony\Component\Validator\Constraints\IsFalse
     */
    protected function createBillingSameAsShippingConstraint(): IsFalse
    {
        return new IsFalse([
            'message' => static::VALIDATION_BILLING_SAME_AS_SHIPPING_MESSAGE,
            'groups' => static::GROUP_BILLING_SAME_AS_SHIPPING,
        ]);
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $form
     *
     * @return bool
     */
    protected function isIdCustomerAddressFieldNotEmpty(FormInterface $form): bool
    {
        return !$form->has(CheckoutAddressForm::FIELD_ID_CUSTOMER_ADDRESS)
            || $form->get(CheckoutAddressForm::FIELD_ID_CUSTOMER_ADDRESS)->getData() !== CheckoutAddressForm::VALUE_ADD_NEW_ADDRESS;
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $form
     *
     * @return bool
     */
    protected function isIdCompanyUnitAddressFieldNotEmpty(FormInterface $form): bool
    {
        return !$form->has(CheckoutAddressForm::FIELD_ID_COMPANY_UNIT_ADDRESS)
            || $form->get(CheckoutAddressForm::FIELD_ID_COMPANY_UNIT_ADDRESS)->getData();
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $form
     *
     * @return bool
     */
    protected function isIdCustomerAddressExistAndNotEmpty(FormInterface $form): bool
    {
        return $form->has(CheckoutAddressForm::FIELD_ID_CUSTOMER_ADDRESS)
            && $form->get(CheckoutAddressForm::FIELD_ID_CUSTOMER_ADDRESS)->getData() !== CheckoutAddressForm::VALUE_ADD_NEW_ADDRESS;
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $form
     *
     * @return bool
     */
    protected function isIdCompanyUnitAddressFieldExistAndNotEmpty(FormInterface $form): bool
    {
        return $form->has(CheckoutAddressForm::FIELD_ID_COMPANY_UNIT_ADDRESS)
            && $form->get(CheckoutAddressForm::FIELD_ID_COMPANY_UNIT_ADDRESS)->getData();
    }
}
