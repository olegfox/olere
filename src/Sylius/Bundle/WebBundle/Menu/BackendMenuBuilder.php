<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Menu;

use Knp\Menu\ItemInterface;
use Sylius\Bundle\WebBundle\Event\MenuBuilderEvent;
use Symfony\Component\HttpFoundation\Request;

/**
 * Main menu builder.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class BackendMenuBuilder extends MenuBuilder
{

    /**
     * Builds backend main menu.
     *
     * @param Request $request
     *
     * @return ItemInterface
     */
    public function createMainMenu(Request $request)
    {
        $menu = $this->factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'nav navbar-nav navbar-right'
            )
        ));

        $childOptions = array(
            'attributes' => array('class' => 'dropdown'),
            'childrenAttributes' => array('class' => 'dropdown-menu'),
            'labelAttributes' => array('class' => 'dropdown-toggle', 'data-toggle' => 'dropdown', 'href' => '#')
        );

        $menu->addChild('dashboard', array(
            'route' => 'sylius_backend_dashboard'
        ))->setLabel($this->translate('sylius.backend.menu.main.dashboard'));

        $this->addAssortmentMenu($menu, $childOptions, 'main');
        $this->addSalesMenu($menu, $childOptions, 'main');
        $this->addCustomersMenu($menu, $childOptions, 'main');
        $this->addContentMenu($menu, $childOptions, 'main');
        $this->addConfigurationMenu($menu, $childOptions, 'main');

        $menu->addChild('homepage', array(
            'route' => 'sylius_homepage'
        ))->setLabel($this->translate('sylius.backend.menu.main.homepage'));

        $menu->addChild('logout', array(
            'route' => 'fos_user_security_logout'
        ))->setLabel($this->translate('sylius.backend.logout'));

        $this->eventDispatcher->dispatch(MenuBuilderEvent::BACKEND_MAIN, new MenuBuilderEvent($this->factory, $menu));

        return $menu;
    }

    /**
     * Builds backend sidebar menu.
     *
     * @param Request $request
     *
     * @return ItemInterface
     */
    public function createSidebarMenu(Request $request)
    {
        $menu = $this->factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'nav'
            )
        ));

        $childOptions = array(
            'childrenAttributes' => array('class' => 'nav'),
            'labelAttributes' => array('class' => 'nav-header')
        );

        $this->addAssortmentMenu($menu, $childOptions, 'sidebar');
        $this->addSalesMenu($menu, $childOptions, 'sidebar');
        $this->addCustomersMenu($menu, $childOptions, 'sidebar');
        if ($this->securityContext->getToken() && $this->securityContext->isGranted('ROLE_SYLIUS_ADMIN')) {
            $this->addContentMenu($menu, $childOptions, 'sidebar');
            $this->addConfigurationMenu($menu, $childOptions, 'sidebar');
        }
        $this->eventDispatcher->dispatch(MenuBuilderEvent::BACKEND_SIDEBAR, new MenuBuilderEvent($this->factory, $menu));

        return $menu;
    }

    /**
     * Add assortment menu.
     *
     * @param ItemInterface $menu
     * @param array $childOptions
     * @param string $section
     */
    protected function addAssortmentMenu(ItemInterface $menu, array $childOptions, $section)
    {
        $child = $menu
            ->addChild('assortment', $childOptions)
            ->setLabel($this->translate(sprintf('sylius.backend.menu.%s.assortment', $section)));

//        if ($this->securityContext->getToken() && $this->securityContext->isGranted('ROLE_SYLIUS_ADMIN')) {
            $child->addChild('taxonomies', array(
                'route' => 'sylius_backend_taxonomy_index',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-folder-close'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.taxonomies', $section)));
//        }
        $child->addChild('products', array(
            'route' => 'sylius_backend_product_index',
            'labelAttributes' => array('icon' => 'glyphicon glyphicon-th-list'),
        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.products', $section)));


            $child->addChild('import', array(
                'route' => 'sylius_backend_import_index',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-compressed'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.import', $section)));

        if ($this->securityContext->getToken() && $this->securityContext->isGranted('ROLE_MANAGER2') == false) {
            $child->addChild('export', array(
                'route' => 'sylius_backend_export_index',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-compressed'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.export', $section)));
        }
    }

    /**
     * Add content menu.
     *
     * @param ItemInterface $menu
     * @param array $childOptions
     * @param string $section
     */
    protected function addContentMenu(ItemInterface $menu, array $childOptions, $section)
    {
        $child = $menu
            ->addChild('content', $childOptions)
            ->setLabel($this->translate(sprintf('sylius.backend.menu.%s.content', $section)));

//        $child->addChild('blocks', array(
//            'route' => 'sylius_backend_block_index',
//            'labelAttributes' => array('icon' => 'glyphicon glyphicon-th-large'),
//        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.blocks', $section)));
        $child->addChild('Pages', array(
            'route' => 'sylius_backend_page_index',
            'labelAttributes' => array('icon' => 'glyphicon glyphicon-th-list'),
        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.pages', $section)));

        $child->addChild('sliderText', array(
            'route' => 'sylius_backend_slider_text_index',
            'labelAttributes' => array('icon' => 'glyphicon glyphicon-compressed'),
        ))->setLabel('Текст на слайдере');

        $child->addChild('slider', array(
            'route' => 'sylius_backend_slider_index',
            "routeParameters" => array("type" => 0),
            'labelAttributes' => array('icon' => 'glyphicon glyphicon-compressed'),
        ))->setLabel('Слайдер на главной Розн.');

        $child->addChild('sliderOpt', array(
            'route' => 'sylius_backend_slider_index',
            "routeParameters" => array("type" => 1),
            'labelAttributes' => array('icon' => 'glyphicon glyphicon-compressed'),
        ))->setLabel('Слайдер на главной Опт.');
    }

    /**
     * Add customers menu.
     *
     * @param ItemInterface $menu
     * @param array $childOptions
     * @param string $section
     */
    protected function addCustomersMenu(ItemInterface $menu, array $childOptions, $section)
    {
        $child = $menu
            ->addChild('customer', $childOptions)
            ->setLabel($this->translate(sprintf('sylius.backend.menu.%s.customer', $section)));

        $child->addChild('users', array(
            'route' => 'sylius_backend_user_index',
            'labelAttributes' => array('icon' => 'glyphicon glyphicon-user'),
        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.users', $section)));

        if ($this->securityContext->getToken() && $this->securityContext->isGranted('ROLE_SYLIUS_ADMIN')) {
            $child->addChild('groups', array(
                'route' => 'sylius_backend_group_index',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-home'),
            ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.groups', $section)));
        }
    }

    /**
     * Add sales menu.
     *
     * @param ItemInterface $menu
     * @param array $childOptions
     * @param string $section
     */
    protected function addSalesMenu(ItemInterface $menu, array $childOptions, $section)
    {
        $child = $menu
            ->addChild('sales', $childOptions)
            ->setLabel($this->translate(sprintf('sylius.backend.menu.%s.sales', $section)));

        $child->addChild('orders_pending', array(
            'route' => 'sylius_backend_order_index',
            "routeParameters" => array("state" => 3),
            'labelAttributes' => array('icon' => 'glyphicon glyphicon-shopping-cart'),
        ))->setLabel('Не подтвержденные');

        $child->addChild('orders_payment', array(
            'route' => 'sylius_backend_order_index',
            "routeParameters" => array("state" => 9),
            'labelAttributes' => array('icon' => 'glyphicon glyphicon-shopping-cart'),
        ))->setLabel('Не оплаченные');

        $child->addChild('orders_cancelled', array(
            'route' => 'sylius_backend_order_index',
            "routeParameters" => array("state" => 7),
            'labelAttributes' => array('icon' => 'glyphicon glyphicon-shopping-cart'),
        ))->setLabel('Отмененные');

        $child->addChild('orders_shipped', array(
            'route' => 'sylius_backend_order_index',
            "routeParameters" => array("state" => 5),
            'labelAttributes' => array('icon' => 'glyphicon glyphicon-shopping-cart'),
        ))->setLabel('Не отгруженные');

        $child->addChild('orders_confirmed', array(
            'route' => 'sylius_backend_order_index',
            "routeParameters" => array("state" => 4),
            'labelAttributes' => array('icon' => 'glyphicon glyphicon-shopping-cart'),
        ))->setLabel('Выполненные');

//        if ($this->securityContext->getToken() && $this->securityContext->isGranted('ROLE_SYLIUS_ADMIN')) {
//            $child->addChild('sales', array(
//                'route' => 'sylius_backend_sale_index',
//                'labelAttributes' => array('icon' => 'glyphicon glyphicon-shopping-cart'),
//            ))->setLabel('Скидки');
//        }
//        $child->addChild('shipments', array(
//            'route' => 'sylius_backend_shipment_index',
//            'labelAttributes' => array('icon' => 'glyphicon glyphicon-plane'),
//        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.shipments', $section)));

        /*$child->addChild('new_order', array(
            'route' => 'sylius_backend_order_create',
            'labelAttributes' => array('icon' => 'glyphicon glyphicon-plus-sign'),
        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.new_order', $section)));*/
//        $child->addChild('payments', array(
//            'route' => 'sylius_backend_payment_index',
//            'labelAttributes' => array('icon' => 'glyphicon glyphicon-credit-card'),
//        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.payments', $section)));

//        $child->addChild('promotions', array(
//            'route' => 'sylius_backend_promotion_index',
//            'labelAttributes' => array('icon' => 'glyphicon glyphicon-bullhorn'),
//        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.promotions', $section)));
//        $child->addChild('new_promotion', array(
//            'route' => 'sylius_backend_promotion_create',
//            'labelAttributes' => array('icon' => 'glyphicon glyphicon-plus-sign'),
//        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.new_promotion', $section)));
    }

    /**
     * Add configuration menu.
     *
     * @param ItemInterface $menu
     * @param array $childOptions
     * @param string $section
     */
    protected function addConfigurationMenu(ItemInterface $menu, array $childOptions, $section)
    {
        $child = $menu
            ->addChild('configuration', $childOptions)
            ->setLabel($this->translate(sprintf('sylius.backend.menu.%s.configuration', $section)));

        $child->addChild('general_settings', array(
            'route' => 'sylius_backend_general_settings',
            'labelAttributes' => array('icon' => 'glyphicon glyphicon-info-sign'),
        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.general_settings', $section)));

//        $child->addChild('locales', array(
//            'route' => 'sylius_backend_locale_index',
//            'labelAttributes' => array('icon' => 'glyphicon glyphicon-flag'),
//        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.locales', $section)));

//        $child->addChild('payment_methods', array(
//            'route' => 'sylius_backend_payment_method_index',
//            'labelAttributes' => array('icon' => 'glyphicon glyphicon-credit-card'),
//        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.payment_methods', $section)));

//        $child->addChild('exchange_rates', array(
//            'route' => 'sylius_backend_exchange_rate_index',
//            'labelAttributes' => array('icon' => 'glyphicon glyphicon-usd'),
//        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.exchange_rates', $section)));

//        $child->addChild('taxation_settings', array(
//            'route' => 'sylius_backend_taxation_settings',
//            'labelAttributes' => array('icon' => 'glyphicon glyphicon-cog'),
//        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.taxation_settings', $section)));

//        $child->addChild('tax_categories', array(
//            'route' => 'sylius_backend_tax_category_index',
//            'labelAttributes' => array('icon' => 'glyphicon glyphicon-cog'),
//        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.tax_categories', $section)));

//        $child->addChild('tax_rates', array(
//            'route' => 'sylius_backend_tax_rate_index',
//            'labelAttributes' => array('icon' => 'glyphicon glyphicon-cog'),
//        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.tax_rates', $section)));

//        $child->addChild('shipping_categories', array(
//            'route' => 'sylius_backend_shipping_category_index',
//            'labelAttributes' => array('icon' => 'glyphicon glyphicon-cog'),
//        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.shipping_categories', $section)));

//        $child->addChild('shipping_methods', array(
//            'route' => 'sylius_backend_shipping_method_index',
//            'labelAttributes' => array('icon' => 'glyphicon glyphicon-cog'),
//        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.shipping_methods', $section)));

//        $child->addChild('countries', array(
//            'route' => 'sylius_backend_country_index',
//            'labelAttributes' => array('icon' => 'glyphicon glyphicon-flag'),
//        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.countries', $section)));

//        $child->addChild('zones', array(
//            'route' => 'sylius_backend_zone_index',
//            'labelAttributes' => array('icon' => 'glyphicon glyphicon-globe'),
//        ))->setLabel($this->translate(sprintf('sylius.backend.menu.%s.zones', $section)));
    }
}
