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

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class SaleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'hidden')
            ->add('date_begin', 'date', array(
                'label' => 'Дата начала'))
            ->add('date_end', 'date', array(
                'label' => "Дата окончания"))
            ->add('percent', 'integer', array(
                'label' => "Процент скидки"))
            ->add('type_price', 'choice', array('label' => "Тип цены", 'choices' => array(
                0 => "Все цены",
                1 => "Только оптовые",
                2 => "Только розничные"
            )))
            ->add('taxon', 'entity', array(
                'label' => 'Каталоги',
                'class' => 'Sylius\Bundle\CoreBundle\Model\Taxon',
                'property' => 'name'
            ))
        ;
    }

    public function getName()
    {
        return 'sylius_sale';
    }
}
