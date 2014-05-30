<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Controller\Backend;

use Sylius\Bundle\OrderBundle\Model\OrderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sylius\Bundle\CoreBundle\Model\Slider;
use Sylius\Bundle\CoreBundle\Form\Type\SliderType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Backend dashboard controller.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class DashboardController extends Controller
{
    /**
     * Backend dashboard display action.
     */
    public function mainAction()
    {
        $orderRepository = $this->get('sylius.repository.order');
        $userRepository = $this->get('sylius.repository.user');

        return $this->render('SyliusWebBundle:Backend/Dashboard:main.html.twig', array(
            'orders_count' => $orderRepository->countBetweenDates(new \DateTime('1 month ago'), new \DateTime()),
            'orders' => $orderRepository->findBy(array(), array('updatedAt' => 'desc'), 5),
            'users' => $userRepository->findBy(array(), array('id' => 'desc'), 5),
            'registrations_count' => $userRepository->countBetweenDates(new \DateTime('1 month ago'), new \DateTime()),
            'sales' => $orderRepository->revenueBetweenDates(new \DateTime('1 month ago'), new \DateTime()),
            'sales_confirmed' => $orderRepository->revenueBetweenDates(new \DateTime('1 month ago'), new \DateTime(), OrderInterface::STATE_CONFIRMED),
        ));
    }

    public function sliderAction(Request $request)
    {
        $slider = new Slider();
        $form = $this->createForm(new SliderType(), $slider);
        $repository = $this->getDoctrine()
            ->getRepository('Sylius\Bundle\CoreBundle\Model\Slider');
        $sliders = $repository->findAll();
        $manager = $this->getDoctrine()->getManager();
        if ($request->isMethod('POST')) {
            $form->bind($request);
            $data = array();
            $i = 0;
            $imagesJson = $request->get('gallery');
            if ($imagesJson != "") {
                $images = json_decode($imagesJson);
                foreach ($images as $image) {
                    $data[$i]["image"] = $image;
                    if ($data[$i]["image"] != "") {
                        $slider = new Slider();
                        $slider->setImage($data[$i]["image"]);
                        $manager->persist($slider);
                    }
                    $i++;
                }
            }
            $manager->flush();
            $sliders = $repository->findAll();
            return $this->render('SyliusWebBundle:Backend/Slider:index.html.twig', array(
                'form' => $form->createView(),
                'sliders' => $sliders
            ));
        }
        return $this->render('SyliusWebBundle:Backend/Slider:index.html.twig', array(
            'form' => $form->createView(),
            'sliders' => $sliders
        ));
    }

    public function deleteSliderAction($id)
    {
        $repository = $this->getDoctrine()
            ->getRepository('Sylius\Bundle\CoreBundle\Model\Slider');
        $slider = $repository->findOneBy(array("id" => $id));
        $manager = $this->getDoctrine()->getManager();

        if($slider){
            if(file_exists("images/slider/".$slider->getImage())){
                unlink("images/slider/".$slider->getImage());
            }
            $manager->remove($slider);
            $manager->flush();
        }

        return $this->redirect($this->generateUrl('sylius_backend_slider_index'));
    }
}
