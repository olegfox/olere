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

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Sylius\Bundle\CartBundle\Provider\CartProviderInterface;
use Sylius\Bundle\MoneyBundle\Twig\SyliusMoneyExtension;
use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;
use Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Intl\Intl;

/**
 * Frontend menu builder.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class FrontendMenuBuilder extends MenuBuilder
{
    /**
     * Currency repository.
     *
     * @var RepositoryInterface
     */
    protected $exchangeRateRepository;

    /**
     * Taxonomy repository.
     *
     * @var RepositoryInterface
     */
    protected $taxonomyRepository;

    /**
     * Cart provider.
     *
     * @var CartProviderInterface
     */
    protected $cartProvider;

    /**
     * Money extension.
     *
     * @var SyliusMoneyExtension
     */
    protected $moneyExtension;

    /**
     * Constructor.
     *
     * @param FactoryInterface $factory
     * @param SecurityContextInterface $securityContext
     * @param TranslatorInterface $translator
     * @param EventDispatcherInterface $eventDispatcher
     * @param RepositoryInterface $exchangeRateRepository
     * @param RepositoryInterface $taxonomyRepository
     * @param CartProviderInterface $cartProvider
     * @param SyliusMoneyExtension $moneyExtension
     */
    public function __construct(
        FactoryInterface $factory,
        SecurityContextInterface $securityContext,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        RepositoryInterface $exchangeRateRepository,
        RepositoryInterface $taxonomyRepository,
        CartProviderInterface $cartProvider,
        SyliusMoneyExtension $moneyExtension
    )
    {
        parent::__construct($factory, $securityContext, $translator, $eventDispatcher);

        $this->exchangeRateRepository = $exchangeRateRepository;
        $this->taxonomyRepository = $taxonomyRepository;
        $this->cartProvider = $cartProvider;
        $this->moneyExtension = $moneyExtension;
    }

    /**
     * Builds frontend pagemenu.
     *
     * @return ItemInterface
     */
    public function createPageMenu()
    {
        $menu = $this->factory->createItem('root');
        $pagemenu = array(
//            "home" => array(
//                "name" => "OLERE.RU",
//                "route" => "sylius_homepage",
//                "routeParameters" => array()
//            ),
            "catalog" => array(
                "name" => "Каталог",
                "route" => "sylius_catalog",
                "routeParameters" => array()
            ),
            "collection" => array(
                "name" => "Коллекции",
                "route" => "sylius_collections",
                "routeParameters" => array()
            ),
            "customers" => array(
                "name" => "Оптовым клиентам",
                "route" => "sylius_page_show",
                "routeParameters" => array("id" => "customers"),
                "subChild" => array(
                    "how_to_order" => array(
                        "name" => "Как сделать заказ",
                        "route" => "sylius_page_sub_show",
                        "routeParameters" => array("id" => "how_to_order", "sub" => "customers")
                    ),
                    "how_to_pay_for_the_order" => array(
                        "name" => "Как оплатить заказ",
                        "route" => "sylius_page_sub_show",
                        "routeParameters" => array("id" => "how_to_pay_for_the_order", "sub" => "customers")
                    ),
                    "shipping_customers" => array(
                        "name" => "Доставка",
                        "route" => "sylius_page_sub_show",
                        "routeParameters" => array("id" => "shipping_customers", "sub" => "customers")
                    ),
                    "download_the_contract" => array(
                        "name" => "Скачать договор",
                        "route" => "sylius_page_sub_show",
                        "routeParameters" => array("id" => "download_the_contract", "sub" => "customers")
                    ),
                    "details" => array(
                        "name" => "Реквизиты",
                        "route" => "sylius_page_sub_show",
                        "routeParameters" => array("id" => "details", "sub" => "customers")
                    )
                )
            ),
            "shipping" => array(
                "name" => "Доставка",
                "route" => "sylius_page_show",
                "routeParameters" => array("id" => "shipping")
            ),
            "about" => array(
                "name" => "О нас",
                "route" => "sylius_page_show",
                "routeParameters" => array("id" => "about"),
                "subChild" => array(
                    "history" => array(
                        "name" => "История",
                        "route" => "sylius_page_sub_show",
                        "routeParameters" => array("id" => "history", "sub" => "about")
                    ),
                    "jobs" => array(
                        "name" => "Вакансии",
                        "route" => "sylius_page_sub_show",
                        "routeParameters" => array("id" => "jobs", "sub" => "about")
                    ),
                    "news" => array(
                        "name" => "Новости",
                        "route" => "sylius_page_sub_show",
                        "routeParameters" => array("id" => "news", "sub" => "about")
                    ),
                    "certificates" => array(
                        "name" => "Сертификаты",
                        "route" => "sylius_page_sub_show",
                        "routeParameters" => array("id" => "certificates", "sub" => "about")
                    ),
                    "partners" => array(
                        "name" => "Партнеры",
                        "route" => "sylius_page_sub_show",
                        "routeParameters" => array("id" => "partners", "sub" => "about")
                    )
                )
            ),
            "contacts" => array(
                "name" => "Контакты",
                "route" => "sylius_page_show",
                "routeParameters" => array("id" => "contacts")
            )
        );
        if ($this->request->getHost() == 'olere.ru') {
            $pagemenu['shipping'] = '';
        } else {
            $pagemenu['customers'] = '';
        }
        $menu->setCurrentUri($this->request->getRequestUri());
        foreach ($pagemenu as $key => $p) {
            if (isset($p["route"])) {
                $m = $menu->addChild($key, array(
                    'route' => $p["route"],
                    'routeParameters' => $p["routeParameters"],
                    'linkAttributes' => array(),
                ))->setLabel($p["name"]);
                if(isset($p["subChild"])){
                    foreach($p["subChild"] as $key => $s){
                        $m->addChild($key, array(
                            'route' => $s["route"],
                            'routeParameters' => $s["routeParameters"],
                            'linkAttributes' => array(),
                        ))->setLabel($s["name"]);
                    }
                }
            }
        }

//        if ($this->cartProvider->hasCart()) {
//            $cart = $this->cartProvider->getCart();
//            $cartTotals = array('items' => $cart->getTotalItems(), 'total' => $cart->getTotal());
//        } else {
//            $cartTotals = array('items' => 0, 'total' => 0);
//        }
//
//        $menu->addChild('cart', array(
//            'route' => 'sylius_cart_summary',
//            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.main.cart', array(
//                    '%items%' => $cartTotals['items'],
//                    '%total%' => $this->moneyExtension->formatPrice($cartTotals['total'])
//                ))),
//            'labelAttributes' => array('icon' => 'icon-shopping-cart icon-large')
//        ))->setLabel($this->translate('sylius.frontend.menu.main.cart'));

//        $menu->addChild("", array(
//            'route' => "",
//            'routeParameters' => "",
//            'linkAttributes' => array(),
//        ))->setLabel("");

        return $menu;
    }

    /**
     * Builds frontend bottom_menu.
     *
     * @return ItemInterface
     */
    public function createBottomMenu()
    {
        $menu = $this->factory->createItem('root');
        $bottom_menu = array(
//            "buy_retail" => array(
//                "name" => "Купить в розницу",
//                "route" => "sylius_page_show",
//                "routeParameters" => array("id" => "buy_retail")
//            ),
            /*"delivery" => array(
                "name" => "Доставка",
                "route" => "sylius_page_show",
                "routeParameters" => array("id" => "delivery")
            ),*/
            "about" => array(
                "name" => "О компании",
                "route" => "sylius_page_show",
                "routeParameters" => array("id" => "about")
            ),
            "certificates_and_documents" => array(
                "name" => "Сертификаты и документы",
                "route" => "sylius_page_show",
                "routeParameters" => array("id" => "certificates_and_documents")
            ),
            "partners" => array(
                "name" => "Партнеры",
                "route" => "sylius_page_show",
                "routeParameters" => array("id" => "partners")
            ),
            "promotions" => array(
                "name" => "Акции и скидки",
                "route" => "sylius_page_show",
                "routeParameters" => array("id" => "promotions")
            ),
            "questions_and_answers" => array(
                "name" => "Вопросы и ответы",
                "route" => "sylius_page_show",
                "routeParameters" => array("id" => "questions_and_answers")
            )
        );
        $menu->setCurrentUri($this->request->getRequestUri());
        if ($this->request->getHost() == 'olere.ru') {
            $menu->addChild('buy_retail', array(
                'uri' => 'http://olere-shop.ru/'
            ))->setLabel("Купить в розницу");
        } else {
            $menu->addChild('buy_retail', array(
                'uri' => 'http://olere.ru/'
            ))->setLabel("Купить оптом");
        }
        foreach ($bottom_menu as $key => $p) {
            $menu->addChild($key, array(
                'route' => $p["route"],
                'routeParameters' => $p["routeParameters"],
                'linkAttributes' => array(),
            ))->setLabel($p["name"]);
        }

//        if ($this->cartProvider->hasCart()) {
//            $cart = $this->cartProvider->getCart();
//            $cartTotals = array('items' => $cart->getTotalItems(), 'total' => $cart->getTotal());
//        } else {
//            $cartTotals = array('items' => 0, 'total' => 0);
//        }
//
//        $menu->addChild('cart', array(
//            'route' => 'sylius_cart_summary',
//            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.main.cart', array(
//                    '%items%' => $cartTotals['items'],
//                    '%total%' => $this->moneyExtension->formatPrice($cartTotals['total'])
//                ))),
//            'labelAttributes' => array('icon' => 'icon-shopping-cart icon-large')
//        ))->setLabel($this->translate('sylius.frontend.menu.main.cart'));

        $menu->addChild("", array(
            'route' => "",
            'routeParameters' => "",
            'linkAttributes' => array(),
        ))->setLabel("");

        return $menu;
    }

    /**
     * Builds frontend main menu.
     *
     * @param Request $request
     *
     * @return ItemInterface
     */
    public function createMainMenu(Request $request)
    {
        $menu = $this->factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'nav nav-pills'
            )
        ));

        if ($this->securityContext->getToken() && $this->securityContext->isGranted('ROLE_USER')) {
            $route = $this->request === null ? '' : $this->request->get('_route');

//            if (1 === preg_match('/^(sylius_account)|(fos_user)/', $route)) {
//                $menu->addChild('shop', array(
//                    'route' => 'sylius_homepage',
//                    'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.account.shop')),
//                    'labelAttributes' => array('icon' => 'icon-th icon-large', 'iconOnly' => false)
//                ))->setLabel($this->translate('sylius.frontend.menu.account.shop'));
//            } else {
//                $menu->addChild('account', array(
//                    'route' => 'sylius_account_homepage',
//                    'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.main.account')),
//                    'labelAttributes' => array('icon' => 'icon-user icon-large', 'iconOnly' => false)
//                ))->setLabel($this->translate('sylius.frontend.menu.main.account'));
//            }

//            $menu->addChild('logout', array(
//                'route' => 'fos_user_security_logout',
//                'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.main.logout')),
//                'labelAttributes' => array('icon' => 'icon-off icon-large', 'iconOnly' => false)
//            ))->setLabel($this->translate('sylius.frontend.menu.main.logout'));
//        } else {
//            $menu->addChild('login', array(
//                'route' => 'fos_user_security_login',
//                'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.main.login-opt')),
//                'labelAttributes' => array('icon' => 'icon-lock icon-large', 'iconOnly' => false)
//            ))->setLabel($this->translate('sylius.frontend.menu.main.login-opt'));
//            $menu->addChild('register', array(
//                'route' => 'fos_user_registration_register',
//                'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.main.register')),
//                'labelAttributes' => array('icon' => 'icon-user icon-large', 'iconOnly' => false)
//            ))->setLabel($this->translate('sylius.frontend.menu.main.register'));
        }

        if ($this->securityContext->getToken() && $this->securityContext->isGranted('ROLE_SYLIUS_ADMIN')) {

            $routeParams = array(
                'route' => 'sylius_backend_dashboard',
                'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.main.administration')),
                'labelAttributes' => array('icon' => 'icon-briefcase icon-large', 'iconOnly' => false)
            );

            if ($this->securityContext->isGranted('ROLE_PREVIOUS_ADMIN')) {
                $routeParams = array_merge($routeParams, array(
                    'route' => 'sylius_switch_user_return',
                    'routeParameters' => array(
                        'username' => $this->securityContext->getToken()->getUsername(),
                        '_switch_user' => '_exit'
                    )
                ));
            }

//            $menu->addChild('administration', $routeParams)->setLabel($this->translate('sylius.frontend.menu.main.administration'));
        }

//        if ($this->cartProvider->hasCart()) {
//            $cart = $this->cartProvider->getCart();
//            $cartTotals = array('items' => $cart->getTotalItems(), 'total' => $cart->getTotal());
//        } else {
//            $cartTotals = array('items' => 0, 'total' => 0);
//        }
//
//        $menu->addChild('cart', array(
//            'route' => 'sylius_cart_summary',
//            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.main.cart', array(
//                    '%items%' => $cartTotals['items'],
//                    '%total%' => $this->moneyExtension->formatPrice($cartTotals['total'])
//                ))),
//            'labelAttributes' => array('icon' => 'icon-shopping-cart icon-large')
//        ))->setLabel($this->translate('sylius.frontend.menu.main.cart'));

        return $menu;
    }

    /**
     * Builds frontend currency menu.
     *
     * @return ItemInterface
     */
    public function createCurrencyMenu()
    {
        $menu = $this->factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'nav nav-pills'
            )
        ));

        foreach ($this->exchangeRateRepository->findAll() as $exchangeRate) {
            $menu->addChild($exchangeRate->getCurrency(), array(
                'route' => 'sylius_currency_change',
                'routeParameters' => array('currency' => $exchangeRate->getCurrency()),
                'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.currency', array('%currency%' => $exchangeRate->getCurrency()))),
            ))->setLabel(Intl::getCurrencyBundle()->getCurrencySymbol($exchangeRate->getCurrency()));
        }

        return $menu;
    }

    /**
     * Builds frontend taxonomies menu.
     *
     * @param Request $request
     *
     * @return ItemInterface
     */
    public function createTaxonomiesMenu(Request $request)
    {
        $menu = $this->factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'nav'
            )
        ));

        $childOptions = array(
            'childrenAttributes' => array('class' => 'nav nav-list'),
            'labelAttributes' => array('class' => 'nav-header'),
        );

        $taxonomies = $this->taxonomyRepository->findAll();

        foreach ($taxonomies as $taxonomy) {
            $child = $menu->addChild($taxonomy->getName(), $childOptions);

//            if ($taxonomy->getRoot()->hasPath()) {
//                $child->setLabelAttribute('data-image', $taxonomy->getRoot()->getPath());
//            }
            $i = 0;
            $i = $this->createTaxonomiesMenuNode($child, $taxonomy->getRoot(), $i);
//            if($i == 0){
//                $menu->removeChild($taxonomy->getName());
//            }
        }

        return $menu;
    }

    private function createTaxonomiesMenuNode(ItemInterface $menu, TaxonInterface $taxon, $i)
    {
        foreach ($taxon->getChildren() as $child) {
//            if(count($child->getProducts()) > 0){
            $explode = explode("/", $child->getPermalink());
            $childMenu = $menu->addChild($child->getName(), array(
                'route' => 'sylius_product_index_by_taxon',
                'routeParameters' => array('page' => 1, 'category' => $explode[0], 'subcategory' => $explode[1]),
                'labelAttributes' => array('icon' => 'icon-angle-right')
            ));
//            if ($child->getPath()) {
//                $childMenu->setLabelAttribute('data-image', $child->getPath());
//            }
            $i = $i + 1;
            $this->createTaxonomiesMenuNode($childMenu, $child, $i);
//            }
        }
        return $i;
    }

    /**
     * Builds frontend social menu.
     *
     * @param Request $request
     *
     * @return ItemInterface
     */
    public function createSocialMenu(Request $request)
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('footer1')->setLabel("2014 Все права защищены");
        $menu->addChild('footer2', array(
            'uri' => 'http://olegmitin.ru/',
            'linkAttributes' => array('target' => '_blank')
        ))->setLabel("Сайт создан командой Олега Митина");
//        $menu->addChild('github', array(
//            'uri' => 'https://github.com/Sylius',
//            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.social.github')),
//            'labelAttributes' => array('icon' => 'icon-github-sign icon-large', 'iconOnly' => true)
//        ));
//        $menu->addChild('twitter', array(
//            'uri' => 'https://twitter.com/Sylius',
//            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.social.twitter')),
//            'labelAttributes' => array('icon' => 'icon-twitter-sign icon-large', 'iconOnly' => true)
//        ));
//        $menu->addChild('facebook', array(
//            'uri' => 'http://facebook.com/SyliusEcommerce',
//            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.social.facebook')),
//            'labelAttributes' => array('icon' => 'icon-facebook-sign icon-large', 'iconOnly' => true)
//        ));

        return $menu;
    }

    /**
     * Creates user account menu
     *
     * @param Request $request
     *
     * @return ItemInterface
     */
    public function createAccountMenu(Request $request)
    {
        $menu = $this->factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'nav'
            )
        ));

        $childOptions = array(
            'childrenAttributes' => array('class' => 'nav nav-list'),
            'labelAttributes' => array('class' => 'nav-header')
        );

        $child = $menu->addChild($this->translate('sylius.account.title'), $childOptions);

        $child->addChild('account', array(
            'route' => 'sylius_account_homepage',
            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.account.homepage')),
            'labelAttributes' => array('icon' => 'icon-home', 'iconOnly' => false)
        ))->setLabel($this->translate('sylius.frontend.menu.account.homepage'));

        $child->addChild('profile', array(
            'route' => 'fos_user_profile_edit',
            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.account.profile')),
            'labelAttributes' => array('icon' => 'icon-info-sign', 'iconOnly' => false)
        ))->setLabel($this->translate('sylius.frontend.menu.account.profile'));

        $child->addChild('password', array(
            'route' => 'fos_user_change_password',
            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.account.password')),
            'labelAttributes' => array('icon' => 'icon-lock', 'iconOnly' => false)
        ))->setLabel($this->translate('sylius.frontend.menu.account.password'));

        $child->addChild('orders', array(
            'route' => 'sylius_account_order_index',
            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.account.orders')),
            'labelAttributes' => array('icon' => 'icon-briefcase', 'iconOnly' => false)
        ))->setLabel($this->translate('sylius.frontend.menu.account.orders'));

        $child->addChild('addresses', array(
            'route' => 'sylius_account_address_index',
            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.account.addresses')),
            'labelAttributes' => array('icon' => 'icon-envelope', 'iconOnly' => false)
        ))->setLabel($this->translate('sylius.frontend.menu.account.addresses'));

        return $menu;
    }
}
