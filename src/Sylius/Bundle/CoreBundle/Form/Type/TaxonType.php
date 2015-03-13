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

use Sylius\Bundle\TaxonomiesBundle\Form\Type\TaxonType as BaseTaxonType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Taxon form type.
 */
class TaxonType extends BaseTaxonType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add(
                'file',
                'file',
                array(
                    'label' => 'sylius.form.taxon.file'
                )
            )
            ->add(
                'file2',
                'file',
                array(
                    'label' => 'Выберите изображение для серебра'
                )
            )
            ->add('metaTitle', 'text', array(
                'required' => false,
                'label' => 'Мета-заголовок'
            ))
            ->add('metaKeywords', 'text', array(
                'required' => false,
                'label' => 'Ключевые слова'
            ))
            ->add('metaDescription', 'text', array(
                'required' => false,
                'label' => 'Мета-описание'
            ))
            ->add('text', 'textarea', array(
                'required' => false,
                'label'    => 'Текст',
            ));
    }
}
