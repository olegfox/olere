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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Products filter form type.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ProductFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'required' => false,
                'label'    => 'sylius.form.product_filter.name',
                'attr'     => array(
                    'placeholder' => 'sylius.form.product_filter.name'
                )
            ))
            ->add('sku', 'text', array(
                'required' => false,
                'label'    => 'sylius.form.product_filter.sku',
                'attr'     => array(
                    'placeholder' => 'sylius.form.product_filter.sku'
                )
            ))
            ->add('priceBegin', 'integer', array(
                'required' => false,
                'label'    => 'Цена Розн.',
                'attr'     => array(
                    'placeholder' => 'Цена Розн. от'
                )
            ))
            ->add('priceEnd', 'integer', array(
                'required' => false,
                'label'    => false,
                'attr'     => array(
                    'placeholder' => 'Цена Розн. до'
                )
            ))
            ->add('priceOptBegin', 'integer', array(
                'required' => false,
                'label'    => 'Цена Опт.',
                'attr'     => array(
                    'placeholder' => 'Цена Опт. от'
                )
            ))
            ->add('priceOptEnd', 'integer', array(
                'required' => false,
                'label'    => false,
                'attr'     => array(
                    'placeholder' => 'Цена Опт. до'
                )
            ))
            ->add('skuCode', 'choice', array(
                'required' => false,
                'label'    => 'Код артикула',
                'empty_value' => 'Код артикула',
                'empty_data'  => null,
                'choices' => array(
                    2 => '2',
                    4 => '4'
                )
            ))
            ->add('enabled', 'choice', array(
                'required' => false,
                'label'    => 'Скрыто?',
                'empty_value' => 'Скрыто?',
                'empty_data'  => null,
                'choices' => array(
                    0 => 'Нет',
                    1 => 'Да'
                )
            ))
            ->add('taxons', 'sylius_taxon_selection')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => null
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_product_filter';
    }
}
