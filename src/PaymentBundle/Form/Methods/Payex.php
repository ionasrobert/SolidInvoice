<?php

declare(strict_types=1);

/*
 * This file is part of SolidInvoice project.
 *
 * (c) Pierre du Plessis <open-source@solidworx.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace SolidInvoice\PaymentBundle\Form\Methods;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class Payex extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'account_number',
            TextType::class,
            [
                'constraints' => new NotBlank(),
            ]
        );

        $builder->add(
            'encryption_key',
            PasswordType::class,
            [
                'always_empty' => false,
                'constraints' => new NotBlank(),
            ]
        );

        $builder->add(
            'sandbox',
            CheckboxType::class,
            [
                'required' => false,
            ]
        );
    }

    public function getBlockPrefix(): string
    {
        return 'payex';
    }
}
