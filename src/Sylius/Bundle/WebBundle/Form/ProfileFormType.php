<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;

/**
 * Profile form.
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class ProfileFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstName', 'text', array('label' => false, 'translation_domain' => 'FOSUserBundle', "attr" => array(
            "placeholder" => "form.name"
        )))
            ->add('city', 'text', array(
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
                'ООО' => 'ООО',
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
        $builder->add('organization', 'text', array('label' => false, 'translation_domain' => 'FOSUserBundle', "attr" => array(
            "placeholder" => "Организация"
        )));
        $builder->add('kpp', 'text', array('label' => false, 'translation_domain' => 'FOSUserBundle', "attr" => array(
            "placeholder" => "КПП"
        )));
        $builder->add('currentAccount', 'text', array('label' => false, 'translation_domain' => 'FOSUserBundle', "attr" => array(
            "placeholder" => "Расчетный счет"
        )));
        $builder->add('bank', 'text', array('label' => false, 'translation_domain' => 'FOSUserBundle', "attr" => array(
            "placeholder" => "Банк"
        )));
        $builder->add('correspondentAccount', 'text', array('label' => false, 'translation_domain' => 'FOSUserBundle', "attr" => array(
            "placeholder" => "Корр. счет"
        )));
        $builder->add('bik', 'text', array('label' => false, 'translation_domain' => 'FOSUserBundle', "attr" => array(
            "placeholder" => "БИК"
        )));
        $builder->add('address', 'text', array('label' => false, 'translation_domain' => 'FOSUserBundle', "attr" => array(
            "placeholder" => "Адрес доставки"
        )))->add('email', 'email', array(
                'label' => false,
                'translation_domain' => 'FOSUserBundle',
                "attr" => array(
                    "placeholder" => "form.email"
                )
            ));
    }

    public function getName()
    {
        return 'sylius_user_profile';
    }
}
