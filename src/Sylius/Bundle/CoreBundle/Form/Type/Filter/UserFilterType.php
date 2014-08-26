<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\CoreBundle\Form\Type\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * User filter form type.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class UserFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('query', 'text', array(
                'label' => 'sylius.form.user_filter.query',
                'required' => false,
                'attr'  => array(
                    'placeholder' => 'sylius.form.user_filter.query'
                )
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
            ->add('dateBegin', 'date', array(
                'label' => 'От',
                'required' => false,
                'widget' => 'choice'
            ))
            ->add('dateEnd', 'date', array(
                'label' => 'До',
                'required' => false,
                'widget' => 'choice'
            ))
        ;
    }

    public function getName()
    {
        return 'sylius_user_filter';
    }
}
