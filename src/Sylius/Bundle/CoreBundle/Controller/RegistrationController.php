<?php

namespace Sylius\Bundle\CoreBundle\Controller;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RegistrationController extends BaseController
{
    public function registerTypeAction(Request $request){
        if ('POST' === $request->getMethod()) {
            if($request->get("type") == 0){
                return new RedirectResponse('/register');
            }else{
                return new RedirectResponse('/opt/register');
            }
        }


        return $this->container->get('templating')->renderResponse('SyliusWebBundle:Frontend/Account:registerType.html.twig');
    }

    public function registerAction(Request $request)
    {
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->container->get('fos_user.registration.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->container->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->remove("inn");
        $form->remove("nameCompany");
        $form->remove("formCompany");
        $form->remove("profileCompany");
        $form->remove("countPoint");
        $form->setData($user);

        if ('POST' === $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);
                $plainPassword = substr(sha1(uniqid(mt_rand(), true)), 0, 6);
                $user->setDateSend(new \DateTime('2099-01-01'));
                $user->setPlainPassword($plainPassword);
                $userManager->updateUser($user);

//                $this->registerMessage($user->getEmail(), $plainPassword);

                if (null === $response = $event->getResponse()) {
                    $url = $this->container->get('router')->generate('sylius_user_registration_confirmed');
                    $response = new RedirectResponse($url);
                }

//                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                return $response;
            }
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Registration:register.html.'.$this->getEngine(), array(
            'form' => $form->createView(),
        ));
    }

    public function registerOptAction(Request $request)
    {
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->container->get('fos_user.registration.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->container->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->remove("inn");
//        $form->remove("nameCompany");
//        $form->remove("formCompany");
        $form->remove("profileCompany");
        $form->remove("countPoint");
//        $form->remove("city");
        $form->setData($user);

        if ('POST' === $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

                $user->addRole('ROLE_USER_OPT');
                $plainPassword = substr(sha1(uniqid(mt_rand(), true)), 0, 6);
                $user->setDateSend(new \DateTime('2099-01-01'));
                $user->setPlainPassword($plainPassword);
                $user->setTextPassword($plainPassword);
                $userManager->updateUser($user);
                $this->registerMessageToManager($user);
//                $this->registerMessage($user->getEmail(), $form['plainPassword']->getData());

                if (null === $response = $event->getResponse()) {
                    $url = $this->container->get('router')->generate('sylius_user_registration_confirmed');
                    $response = new RedirectResponse($url);
                }

//                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                return $response;
            }
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Registration:registerOpt.html.'.$this->getEngine(), array(
            'form' => $form->createView(),
        ));
    }

    public function sendPasswordAction($id){
        $repository = $this->getDoctrine()->getRepository('Sylius\Bundle\CoreBundle\Model\User');
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $repository->find($id);
        if(is_object($user)){
            $user->setDateSend(new \DateTime());
//            if(count($user->getTextPassword()) <= 0){
//                $user
//            }
            $userManager->updateUser($user);
            $this->registerMessage($user->getEmail(), $user->getTextPassword());
            return new Response('Письмо успешно отправлено!');
        }else{
            throw new NotFoundHttpException("Пользователь не найден!");
        }
    }

    public function registerMessage($email, $password){
        $mailer = $this->container->get('mailer');
        $message = \Swift_Message::newInstance()
            ->setSubject('Olere')
            ->setFrom(array('order@olere.ru' => "Olere"))
            ->setTo($email)
            ->setBody($this->container->get('templating')->render('SyliusWebBundle:Email:register.html.twig', array('email' => $email, 'password' => $password)), 'text/html');
        $mailer->send($message);
    }

    public function registerMessageToManager($user){
        $mailer = $this->container->get('mailer');
        $message = \Swift_Message::newInstance()
            ->setSubject('Новая регистрация на сайте Olere')
            ->setFrom(array('order@olere.ru' => "Olere"))
            ->setTo("order@olere.ru")
            ->setBody($this->container->get('templating')->render('SyliusWebBundle:Email:registerMessageToManager.html.twig', array('user' => $user)), 'text/html');
        $mailer->send($message);
    }

    public function registrationConfirmedAction(){
//        return $this->container->render('FOSUserBundle:Registration:confirmed.html.twig');
        return $this->container->get('templating')->renderResponse('FOSUserBundle:Registration:confirmed.html.twig');
    }

    public function getDoctrine()
    {
        if (!$this->container->has('doctrine')) {
            throw new \LogicException('The DoctrineBundle is not registered in your application.');
        }

        return $this->container->get('doctrine');
    }
}
