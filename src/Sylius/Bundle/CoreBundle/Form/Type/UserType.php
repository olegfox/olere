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

use FOS\UserBundle\Form\Type\ProfileFormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class UserType extends ProfileFormType
{
    /** @var string */
    private $dataClass;

    /**
     * {@inheritdoc}
     */
    public function __construct($dataClass)
    {
        $this->dataClass = $dataClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', 'text', array(
                'label' => 'sylius.form.user.first_name'
            ))
            ->add('lastName', 'text', array(
                'label' => 'sylius.form.user.last_name'
            ))
            ->add('inn', 'text', array(
                'label' => 'Инн'
            ))
            ->add('phone', 'text', array(
                'label' => 'Телефон'
            ))
            ->add('profileCompany', 'text', array(
                'label' => 'Профиль компании'
            ))
            ->add('countPoint', 'text', array(
                'label' => 'Количество торговых точек'
            ))
            ->add('organization', 'text', array(
                'label' => 'Организация'
            ))
            ->add('kpp', 'text', array(
                'label' => 'КПП'
            ))
            ->add('currentAccount', 'text', array(
                'label' => 'Расчетный счет'
            ))
            ->add('bank', 'text', array(
                'label' => 'Банк'
            ))
            ->add('correspondentAccount', 'text', array(
                'label' => 'Корр. счет'
            ))
            ->add('bik', 'text', array(
                'label' => 'Бик'
            ))
            ->add('city', 'text', array(
                'label' => 'Введите название города',
                'translation_domain' => 'FOSUserBundle',
                'attr' => array(
                    "placeholder" => "Введите название города"
                )
            ))
            ->add('nameCompany', 'text', array('label' => 'form.nameCompany', 'translation_domain' => 'FOSUserBundle', "attr" => array(
                "placeholder" => "form.nameCompany"
            )))
            ->add('formCompany', 'choice', array(
                'choices' => array(
                    'ООО' => 'ООО',
                    'ИП' => 'ИП',
                    'ЗАО' => 'ЗАО',
                    'ГК' => 'ГК',
                    'Совместная закупка' => 'Совместная закупка',
                ),
                'label' => 'form.formCompany',
                'translation_domain' => 'FOSUserBundle',
                'empty_value' => 'form.formCompany'
            ));

        $this->buildUserForm($builder, $options);

        $builder
            ->add('plainPassword', 'password', array(
                'label' => 'sylius.form.user.password'
            ))
            ->add('enabled', 'checkbox', array(
                'label' => 'sylius.form.user.enabled'
            ))
            ->add('groups', 'sylius_group_choice', array(
                'label' => 'sylius.form.user.groups',
                'multiple' => true,
                'required' => false
            ))
            ->add('status', 'choice', array(
                'label' => 'Статус',
                'required' => false,
                'choices' => array(
                    1 => 'Реальный',
                    2 => 'В разработке',
                    3 => 'Бесперспективный',
                    4 => 'Розничный'
                )
            ))
            ->add('action', 'choice', array(
                'label' => 'Показывать акцию?',
                'required' => false,
                'choices' => array(
                    false => 'Нет',
                    true => 'Да'
                )
            ))
            ->add('roles', 'collection', array(
                'label' => 'Роль',
                'required' => true,
//                'choices' => array(
//                    'ROLE_SYLIUS_ADMIN' => 'Администратор',
//                    'ROLE_SYLIUS_ADMIN, ROLE_MANAGER' => 'Менеджер',
//                    '' => 'Розничный покупатель',
//                    'ROLE_USER_OPT' => 'Оптовый покупатель'
//                )
            ))
            ->remove('username');
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->dataClass,
            'validation_groups' => function (FormInterface $form) {
                    $data = $form->getData();
                    $groups = array('Profile', 'sylius');
                    if ($data && !$data->getId()) {
                        $groups[] = 'ProfileAdd';
                    }

                    return $groups;
                },
            'cascade_validation' => true,
            'intention' => 'profile',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_user';
    }
}
