<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sylius\Bundle\CoreBundle\Form\Type\SaleType;
use Sylius\Bundle\CoreBundle\Model\Sale;


class SaleController extends Controller
{
    public function indexAction()
    {
        $sales = $this->getDoctrine()->getRepository('Sylius\Bundle\CoreBundle\Model\Sale')->findAll();
        $params = array(
            'sales' => $sales
        );
        return $this->render('SyliusWebBundle:Backend/Sale:index.html.twig', $params);
    }

    public function createAction(Request $request)
    {
        $sale = new Sale();
        $form = $this->createForm(new SaleType(), $sale);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $sale->setTaxonId($sale->getTaxon()->getId());
            $em = $this->getDoctrine()->getManager();
            $em->persist($sale);
            $em->flush();

            return $this->redirect($this->generateUrl('sylius_backend_sale_index'));
        }

        $params = array(
            'form' => $form->createView()
        );

        return $this->render('SyliusWebBundle:Backend/Sale:create.html.twig', $params);
    }

    public function updateAction(Request $request, $id)
    {
        $sale = $this->getDoctrine()->getRepository('Sylius\Bundle\CoreBundle\Model\Sale')->find($id);
        if ($sale->getTaxonId() != NULL) {
            $taxon = $this->getDoctrine()->getRepository('Sylius\Bundle\CoreBundle\Model\Taxon')->find($sale->getTaxonId());
            if ($taxon) {
                $sale->setTaxon($taxon);
            }
        }
        $form = $this->createForm(new SaleType(), $sale);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $sale->setTaxonId($sale->getTaxon()->getId());
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirect($this->generateUrl('sylius_backend_sale_index'));
        }

        $params = array(
            'sale' => $sale,
            'form' => $form->createView()
        );

        return $this->render('SyliusWebBundle:Backend/Sale:update.html.twig', $params);
    }

    public function deleteAction($id)
    {
        $sale = $this->getDoctrine()->getRepository('Sylius\Bundle\CoreBundle\Model\Sale')->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($sale);
        $em->flush();
        return $this->redirect($this->generateUrl('sylius_backend_sale_index'));
    }
}
