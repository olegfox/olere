<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Checkout;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;

/**
 * Checkout addressing step form type.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ContactsStepType extends AbstractType
{
    protected $dataClass;

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
            ->add('city', 'entity', array(
                'required' => 'required',
                'label' => 'Город',
                'class' => 'Sylius\Bundle\CoreBundle\Model\City',
                'empty_value' => 'Укажите свой город',
                'empty_data'  => null,
                'attr' => array(
                    'ng-model' => 'ord.city',
                )
            ))
            ->add('delivery', 'choice', array(
                'required' => 'required',
                'choices' => array(
                    0 => "курьер по МСК в предлах МКАД.",
                    1 => "самовывоз"
                ),
                'label' => 'Способ доставки',
                'expanded' => true
            ))
            ->add('address', 'text', array(
                'required' => 'required',
                'attr' => array(
                    'placeholder' => 'Адрес доставки',
                    'ng-model' => 'ord.address',
                )
            ))
            ->add('transport', 'choice', array(
                'required' => 'required',
                'label' => 'Транспортные компании',
                'choices' => array(
                    0 => 'Деловые линии',
                    1 => 'Байкал сервис',
                    2 => 'Пони экспресс',
                    3 => 'Почта России'
                ),
                'empty_value' => 'Выберите транспортную компанию',
                'empty_data'  => null,
                'attr' => array(
                    'ng-model' => 'ord.transport',
                )
            ))
            ->add('username', 'text', array(
                'required' => 'required',
                "attr" => array(
                    'ng-model' => 'ord.username',
                    'placeholder' => 'Имя'
                )
            ))
            ->add('email', 'email', array(
                'required' => 'required',
                'attr' => array(
                    'ng-model' => 'ord.email',
                    'placeholder' => 'Электронная почта'
                )
            ))
            ->add('phone', 'text', array(
                'required' => 'required',
                'attr' => array(
                    'ng-model' => 'ord.phone',
                    'placeholder' => 'Номер телефона'
                )
            ))
            ->add('timeBegin', 'time', array(
                'required' => '',
                'input'  => 'datetime',
                'widget' => 'choice'
            ))
            ->add('timeEnd', 'time', array(
                'required' => '',
                'input'  => 'datetime',
                'widget' => 'choice'
            ))
            ->add('comment', 'textarea', array(
                'required' => '',
                'attr' => array(
                    'ng-model' => 'ord.comment',
                    'placeholder' => 'Комментарий'
                )
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => $this->dataClass,
                'cascade_validation' => true
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_checkout_contacts';
    }
}
