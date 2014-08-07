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
             AND t.parent = 8
            ')->setParameters(array(
                'search' => '%'.$search.'%'
            ))->getResult();
        $collections = $em->createQuery(
            'SELECT t
             FROM Sylius\Bundle\CoreBundle\Model\Taxon t
             WHERE t.name LIKE :search
             AND t.parent = 18
            ')->setParameters(array(
                'search' => '%'.$search.'%'
            ))->getResult();
        $products = $em->createQuery(
            'SELECT p
             FROM Sylius\Bundle\CoreBundle\Model\Product p
             JOIN p.variants v
             WHERE p.name LIKE :search
             OR v.sku LIKE :search
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
                ->setFrom(array('info@olere.ru' => "Olere"))
//                ->setTo('info@olere.ru')
                ->setTo('info@olere.ru')
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
        $sliders = $repository->findAll();
        return $this->render('SyliusWebBundle:Frontend/Homepage:main.html.twig', array(
//            "page" => $page,
            "sliders" => $sliders
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
        return $this->render('SyliusWebBundle:Frontend/Homepage:catalog.html.twig');
    }

    public function collectionsAction(Request $request){
        return $this->render('SyliusWebBundle:Frontend/Homepage:collections.html.twig');
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
