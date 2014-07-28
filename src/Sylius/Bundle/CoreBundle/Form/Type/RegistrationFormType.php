<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstName', 'text', array('label' => false, 'translation_domain' => 'FOSUserBundle', "attr" => array(
            "placeholder" => "form.name"
        )));
        $builder->add('city', 'text', array(
            'label' => false,
            'translation_domain' => 'FOSUserBundle',
            'attr' => array(
                "placeholder" => "Введите название города"
            )
        ));
        $builder->add('phone', 'text', array('label' => false, 'translation_domain' => 'FOSUserBundle', "attr" => array(
            "placeholder" => "form.phone"
        )));
        $builder->add('inn', 'text', array('label' => false, 'translation_domain' => 'FOSUserBundle', "attr" => array(
            "placeholder" => "form.inn"
        )));
        $builder->add('nameCompany', 'text', array('label' => false, 'translation_domain' => 'FOSUserBundle', "attr" => array(
            "placeholder" => "form.nameCompany"
        )));
        $builder->add('formCompany', 'choice', array(
            'choices' => array(
                'ООО2' => 'ООО',
                'ИП' => 'ИП',
                'ЗАО' => 'ЗАО',
                'ГК' => 'ГК',
                'Совместная закупка' => 'Совместная закупка',
            ),
            'label' => false,
            'translation_domain' => 'FOSUserBundle',
            'empty_value' => 'form.formCompany'
        ));
        $builder->add('profileCompany', 'text', array('label' => false, 'translation_domain' => 'FOSUserBundle', "attr" => array(
            "placeholder" => "form.profileCompany"
        )));
        $builder->add('countPoint', 'number', array('label' => false, 'translation_domain' => 'FOSUserBundle', "attr" => array(
            "placeholder" => "form.countPoint"
        )));


        parent::buildForm($builder, $options);

        // remove the username field
        $builder->remove('username');
        $builder->remove('plainPassword');
        $builder->add('plainPassword', 'repeated', array(
            'type' => 'password',
            'options' => array('translation_domain' => 'FOSUserBundle'),
            'first_options' => array('label' => false, 'attr' => array(
                'placeholder' => 'form.password',
                'ng-maxlength' => '10',
                'ng-minlength' => '5',
                'ng-model' => 'user.password'
            ), 'required' => 'required'),
            'second_options' => array('label' => false, 'attr' => array(
                'placeholder' => 'form.password_confirmation',
                'ng-maxlength' => '10',
                'ng-minlength' => '5',
                'ng-model' => 'user.passwordConfirm',
                'password-match' => 'user.password'
            ), 'required' => 'required'),
            'invalid_message' => '',
        ));
    }

    public function getName()
    {
        return 'sylius_user_registration';
    }
}
