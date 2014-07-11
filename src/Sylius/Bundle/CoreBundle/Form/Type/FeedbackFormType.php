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
 * Profile form.
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class FeedbackFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', 'text', array('label' => false, 'translation_domain' => 'FOSUserBundle', "attr" => array(
                "placeholder" => "form.name"
            )))
            ->add('phone', 'text', array('label' => false, 'translation_domain' => 'FOSUserBundle', "attr" => array(
                "placeholder" => "form.phone"
            )))
            ->add('email', 'email', array('label' => false, 'translation_domain' => 'FOSUserBundle', "attr" => array(
                "placeholder" => "form.email2"
            )))
        ;
    }

    public function getName()
    {
        return 'sylius_feedback';
    }
}
