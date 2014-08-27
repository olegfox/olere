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

use Sylius\Bundle\OrderBundle\Form\Type\OrderType as BaseOrderType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Order form type.
 * We add two addresses to form, and that's all.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderType extends BaseOrderType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        parent::buildForm($builder, $options);

        $builder
//            ->add('shippingAddress', 'sylius_address')
//            ->add('billingAddress', 'sylius_address')
            ->add('city', 'text', array(
                'required' => 'required',
                'label' => 'Город',
                'attr' => array(
                    'ng-model' => 'ord.city',
                    'placeholder' => 'Укажите свой город'
                )
            ))
            ->add('delivery', 'choice', array(
                'required' => 'required',
                'choices' => array(
                    0 => "курьер по МСК в предлах МКАД.",
                    1 => "самовывоз"
                ),
                'label' => 'Способ доставки'
            ))
            ->add('address', 'text', array(
                'required' => 'required',
                'label' => 'Адрес доставки',
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
                'required' => false,
                'label' => 'Имя',
                "attr" => array(
                    'ng-model' => 'ord.username',
                    'placeholder' => 'Имя'
                )
            ))
            ->add('email', 'email', array(
                'required' => 'required',
                'label' => 'Электронная почта',
                'attr' => array(
                    'ng-model' => 'ord.email',
                    'placeholder' => 'Электронная почта'
                )
            ))
            ->add('phone', 'text', array(
                'required' => 'required',
                'label' => 'Номер телефона',
                'attr' => array(
                    'ng-model' => 'ord.phone',
                    'placeholder' => 'Номер телефона'
                )
            ))
            ->add('date', 'date', array(
                'widget' => 'choice',
                'years' => range(Date('Y'), 2014),
                'label' => 'Дата доставки'
            ))
            ->add('time', 'choice', array(
                'required' => '',
                'choices' => array(
                    'с 09:00 до 12:00' => 'с 09:00 до 12:00',
                    'с 12:00 до 15:00' => 'с 12:00 до 15:00',
                    'с 15:00 до 18:00' => 'с 15:00 до 18:00'
                ),
                'label' => 'Время доставки'
            ))
            ->add('comment', 'textarea', array(
                'required' => '',
                'label' => 'Комментарий',
                'attr' => array(
                    'ng-model' => 'ord.comment',
                    'placeholder' => 'Комментарий'
                )
            ))
        ;
    }
}
