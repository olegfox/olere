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

class OrderCommentType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', 'textarea', array(
                'label' => 'Тест комментария',
                "attr" => array(
                    'class' => 'ckeditor',
                    'id' => 'ckeditor'
                )
            ))
        ;
    }

    public function getName()
    {
        return 'sylius_order_comment';
    }
}
