<?php

namespace Sylius\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class NewsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'label' => 'Заголовок',
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Заголовок',
                    'class' => 'form-control'
                )
            ))
            ->add('keyword', 'text', array(
                'label' => 'Ключевые слова',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Ключевые слова',
                    'class' => 'form-control'
                )
            ))
            ->add('description', 'textarea', array(
                'label' => 'Описание',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Описание',
                    'class' => 'form-control'
                )
            ))
            ->add('created', 'date', array(
                'required' => true,
                'label' => 'Дата',
                'input'  => 'datetime',
                'widget' => 'choice',
                'attr' => array(
                    'class' => 'form-control'
                )
            ))
            ->add('image', 'file', array(
                'label' => 'Изображения для галереи',
                "attr" => array(
                    "accept" => "image/*",
                    "multiple" => "multiple",
                )
            ))
            ->add('videoFile', 'text', array(
                'label' => 'Видео',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Код видео',
                    'class' => 'form-control'
                )
            ))
            ->add('text', 'textarea', array(
                'label' => 'Текст',
                'required' => false,
                'attr' => array(
                    'class' => 'ckeditor'
                )
            ));
    }

    public function getName()
    {
        return 'news';
    }
}