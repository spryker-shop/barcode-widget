<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\CommentWidget\Form;

use Generated\Shared\Transfer\CommentTransfer;
use Spryker\Yves\Kernel\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @method \SprykerShop\Yves\CommentWidget\CommentWidgetConfig getConfig()
 */
class CommentForm extends AbstractType
{
    public const COMMENT_FORM = 'commentForm';

    protected const GLOSSARY_KEY_COMMENT_WIDGET_MESSAGE_LENGTH_EXCEEDED = 'comment_widget.message.length.exceeded';

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CommentTransfer::class,
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return static::COMMENT_FORM;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->addUuidFiled($builder)
            ->addMessageFiled($builder)
            ->addCommentTags($builder);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addUuidFiled(FormBuilderInterface $builder)
    {
        $builder->add(CommentTransfer::UUID, HiddenType::class);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addMessageFiled(FormBuilderInterface $builder)
    {
        $builder->add(CommentTransfer::MESSAGE, TextareaType::class, [
            'label' => false,
            'constraints' => [
                new NotBlank(),
                new Length([
                    'max' => 5000,
                    'maxMessage' => static::GLOSSARY_KEY_COMMENT_WIDGET_MESSAGE_LENGTH_EXCEEDED,
                ]),
            ],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addCommentTags(FormBuilderInterface $builder)
    {
        $builder->add(CommentTransfer::TAGS, CollectionType::class, [
            'required' => false,
            'label' => false,
            'entry_type' => CommentTagSubForm::class,
        ]);

        return $this;
    }
}
