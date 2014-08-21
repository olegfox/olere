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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Simple page type.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class PageType extends AbstractType
{
    private $dataClass;

    public function __construct($dataClass)
    {
        $this->dataClass = $dataClass;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'text', array(
                'label' => 'sylius.form.page.id'
            ))
            ->add('enable', 'choice', array(
                'label' => 'Скрыть',
                'choices' => array(
                    0 => 'Нет',
                    1 => 'Да'
                ),
                'required' => true
            ))
            ->add('typeContent', 'choice', array(
                'label' => 'Тип страницы',
                'choices' => array(
                    0 => 'Страница',
                    1 => 'Блок'
                ),
                'required' => true
            ))
            ->add('typeMenu', 'choice', array(
                'label' => 'Тип меню',
                'choices' => array(
                    0 => 'Верхнее',
                    1 => 'Нижнее'
                ),
                'required' => true
            ))
            ->add('link', 'text', array(
                'label' => 'Ссылка'
            ))
            ->add('sub', 'text', array(
                'label' => 'Родитель'
            ))
            ->add('position', 'integer', array(
                'label' => 'Позиция'
            ))
            ->add('title', 'text', array(
                'label' => 'sylius.form.page.title'
            ))
            ->add('metaTitle', 'text', array(
                'label' => 'Заголовок (title страницы)'
            ))
            ->add('metaKeywords', 'text', array(
                'label' => 'Ключевые слова'
            ))
            ->add('metaDescription', 'textarea', array(
                'label' => 'Мета-описание'
            ))
            ->add('body', 'textarea', array(
                'required' => false,
                'label'    => 'sylius.form.page.body',
            ))
        ;

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class'        => $this->dataClass,
                'validation_groups' => array('sylius')
            )
        );
    }

    public function getName()
    {
        return 'sylius_page';
    }
}
