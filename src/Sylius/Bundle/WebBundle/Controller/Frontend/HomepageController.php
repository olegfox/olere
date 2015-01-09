<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\ArrayAdapter;
use Sylius\Bundle\CoreBundle\Model\Feedback;
use Sylius\Bundle\CoreBundle\Form\Type\FeedbackFormType;

/**
 * Frontend homepage controller.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class HomepageController extends Controller
{
    public function searchAction(Request $request){
        $search = $request->get('text');
        $em = $this->getDoctrine()->getManager();
        $catalogs = $em->createQuery(
            'SELECT t
             FROM Sylius\Bundle\CoreBundle\Model\Taxon t
             WHERE t.name LIKE :search
             AND t.taxonomy = 8
             AND t.parent > 0
            ')->setParameters(array(
                'search' => '%'.$search.'%'
            ))->getResult();
        $collections = $em->createQuery(
            'SELECT t
             FROM Sylius\Bundle\CoreBundle\Model\Taxon t
             WHERE t.name LIKE :search
             AND t.taxonomy = 9
             AND t.parent > 0
            ')->setParameters(array(
                'search' => '%'.$search.'%'
            ))->getResult();
        $products = $em->createQuery(
            'SELECT p
             FROM Sylius\Bundle\CoreBundle\Model\Product p
             JOIN p.variants v
             JOIN p.taxons t
             WHERE (p.name LIKE :search
             OR v.sku LIKE :search)
             AND (p.enabled = 0 or p.enabled is NULL)
             AND (p.action = 0 or p.action is NULL)
             GROUP BY v.sku
            ')->setParameters(array(
                'search' => '%'.$search.'%'
            ))->getResult();
        $groups = $this->get('sylius.repository.group')->findAll();
        return $this->render('SyliusWebBundle:Frontend/Homepage:search.html.twig', array(
            'catalogs' => $catalogs,
            'collections' => $collections,
            'products' => $products,
            'groups' => $groups
        ));
    }

    public function feedbackAction(Request $request){
        $feedback = new Feedback();
        $form = $this->createForm(new FeedbackFormType(), $feedback);
        if ($request->isMethod('POST')) {
            $form->bind($request);
            $name = $form['firstName']->getData();
            $phone = $form['phone']->getData();
            $email = $form['email']->getData();

            $message = \Swift_Message::newInstance()
                ->setSubject('Olere')
                ->setFrom(array('order@olere.ru' => "Olere"))
//                ->setTo('info@olere.ru')
                ->setTo('order@olere.ru')
                ->setBody(
                    $this->renderView(
                        'SyliusWebBundle:Email:feedback.html.twig',
                        array(
                            'name' => $name,
                            'phone' => $phone,
                            'email' => $email
                        )
                    )
                    , 'text/html'
                )
            ;
            $this->get('mailer')->send($message);

            return $this->render('SyliusWebBundle:Frontend/Homepage:feedback.html.twig', array(
                'form' => $form->createView(),
                'send' => 1
            ));

        }
        return $this->render('SyliusWebBundle:Frontend/Homepage:feedback.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * Store front page.
     *
     * @return Response
     */
    public function mainAction(Request $request)
    {
//        $response = $this->forward('sylius.controller.page:showAction', array(
//            "_sylius" => array(
//                "template" => "SyliusWebBundle:Frontend/Page:show.html.twig"
//            )
//        ));
//        $page = $this->get('sylius.repository.page')->findPage("main");
        if($request->getHost() == "olere-shop.ru"){
            return $this->main2Action();
        }
        $repository = $this->getDoctrine()
            ->getRepository('Sylius\Bundle\CoreBundle\Model\Slider');
        $repositorySliderText = $this->getDoctrine()
            ->getRepository('Sylius\Bundle\CoreBundle\Model\SliderText');
        $repositoryPage = $this->get('sylius.repository.page');

        $page1 = $repositoryPage->findPage('main1');
        $page2 = $repositoryPage->findPage('main2');

        if($request->getHost() == "olere.ru" || $request->getHost() == "www.olere.ru"){
            $sliders = $repository->findBy(array('type' => 1));
        }else{
            $sliders = $repository->findBy(array('type' => 0));
        }
        $sliderText = $repositorySliderText->findAll();
        return $this->render('SyliusWebBundle:Frontend/Homepage:main.html.twig', array(
//            "page" => $page,
            "sliders" => $sliders,
            "sliderText" => $sliderText[0],
            "page1" => $page1,
            "page2" => $page2
        ));
    }

    public function main2Action()
    {
//        $response = $this->forward('sylius.controller.page:showAction', array(
//            "_sylius" => array(
//                "template" => "SyliusWebBundle:Frontend/Page:show.html.twig"
//            )
//        ));
//        $page = $this->get('sylius.repository.page')->findPage("main");
        $repository = $this->getDoctrine()
            ->getRepository('Sylius\Bundle\CoreBundle\Model\Slider');
        $sliders = $repository->findAll();
        return $this->render('SyliusWebBundle:Frontend/Homepage:main2.html.twig', array(
//            "page" => $page,
            "sliders" => $sliders
        ));
    }

    public function catalogAction(Request $request){
        $repositoryPage = $this->get('sylius.repository.page');

        $page = $repositoryPage->findPage('catalogBlock');
        return $this->render('SyliusWebBundle:Frontend/Homepage:catalog.html.twig', array(
            'page' => $page
        ));
    }

    public function collectionsAction(Request $request){
        $repositoryPage = $this->get('sylius.repository.page');

        $page = $repositoryPage->findPage('collectionsBlock');
        return $this->render('SyliusWebBundle:Frontend/Homepage:collections.html.twig', array(
            'page' => $page
        ));
    }

    public function silverAction(Request $request){
        $repositoryPage = $this->get('sylius.repository.page');

        $page = $repositoryPage->findPage('silverBlock');
        return $this->render('SyliusWebBundle:Frontend/Homepage:silver.html.twig', array(
            'page' => $page
        ));
    }

    /**
     * Store front page.
     *
     * @return Response
     */
    public function catalogProductsAction(Request $request, $page, $slug)
    {
        $repository = $this->container->get('sylius.repository.product');
        $name = $request->get("name");
        $sorting = $request->get("sorting");
        if ($name != "") {
            $products = $repository->findForName($name);
        } else {
            if (isset($sorting['name'])) {
                $products = $repository->findBy(array(), array("name" => $sorting['name']));
            } elseif (isset($sorting['price'])) {
                $products = $repository->findForPrice($sorting['price']);
            }else{
                $products = $repository->findAll();
            }
        }
        $adapter = new ArrayAdapter($products);
        $pagerfanta = new Pagerfanta($adapter);
        if ($name != "") {
            $pagerfanta->setMaxPerPage(100000);
        } else {
            $pagerfanta->setMaxPerPage(15);
        }
        if ($page) {
            $pagerfanta->setCurrentPage($page);
        } else {
            $pagerfanta->setCurrentPage(1);
        }
        if ($slug != '') {
            $f = 0;
            foreach ($pagerfanta as $p) {
                if ($p->getSlug() == $slug) {
                    $f = 1;
                }
            }
            if ($f == 0) {
                return $this->redirect($this->generateUrl('sylius_catalog', array("page" => $page)));
            }
        }
        return $this->render('SyliusWebBundle:Frontend/Homepage:catalogProducts.html.twig', array(
            'products' => $pagerfanta,
            'slug' => $slug
        ));
    }

    /**
     * Store front page.
     *
     * @return Response
     */
    public function collectionsProductsAction(Request $request, $page, $slug)
    {
        $repository = $this->container->get('sylius.repository.product');
        $name = $request->get("name");
        $sorting = $request->get("sorting");
        if ($name != "") {
            $products = $repository->findForName($name);
        } else {
            if (isset($sorting['name'])) {
                $products = $repository->findBy(array(), array("name" => $sorting['name']));
            } elseif (isset($sorting['price'])) {
                $products = $repository->findForPrice($sorting['price']);
            }else{
                $products = $repository->findAll();
            }
        }
        $adapter = new ArrayAdapter($products);
        $pagerfanta = new Pagerfanta($adapter);
        if ($name != "") {
            $pagerfanta->setMaxPerPage(100000);
        } else {
            $pagerfanta->setMaxPerPage(15);
        }
        if ($page) {
            $pagerfanta->setCurrentPage($page);
        } else {
            $pagerfanta->setCurrentPage(1);
        }
        if ($slug != '') {
            $f = 0;
            foreach ($pagerfanta as $p) {
                if ($p->getSlug() == $slug) {
                    $f = 1;
                }
            }
            if ($f == 0) {
                return $this->redirect($this->generateUrl('sylius_catalog', array("page" => $page)));
            }
        }
        return $this->render('SyliusWebBundle:Frontend/Homepage:catalogProducts.html.twig', array(
            'products' => $pagerfanta,
            'slug' => $slug
        ));
    }
}
