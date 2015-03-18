<?php

namespace Sylius\Bundle\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sylius\Bundle\CoreBundle\Model\Baner;
use Sylius\Bundle\CoreBundle\Form\Type\BanerType;

/**
 * Baner controller.
 *
 */
class BanerController extends Controller
{

    /**
     * Lists all Baner entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('Sylius\Bundle\CoreBundle\Model\Baner')->findAll();

        return $this->render('SyliusWebBundle:Backend/Baner:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Baner entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Baner();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('sylius_backend_baner_index'));
        }

        return $this->render('SyliusWebBundle:Backend/Baner:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Baner entity.
     *
     * @param Baner $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Baner $entity)
    {
        $form = $this->createForm(new BanerType(), $entity, array(
            'action' => $this->generateUrl('sylius_backend_baner_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array(
            'label' => 'sylius.baner.create_header',
            'attr' => array(
                'class' => 'btn btn-primary btn-lg'
            )
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Baner entity.
     *
     */
    public function newAction()
    {
        $entity = new Baner();
        $form   = $this->createCreateForm($entity);

        return $this->render('SyliusWebBundle:Backend/Baner:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Baner entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('Sylius\Bundle\CoreBundle\Model\Baner')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Baner не найден');
        }

        $editForm = $this->createEditForm($entity);

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SyliusWebBundle:Backend/Baner:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Baner entity.
    *
    * @param Baner $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Baner $entity)
    {
        $form = $this->createForm(new BanerType(), $entity, array(
            'action' => $this->generateUrl('sylius_backend_baner_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array(
            'label' => 'sylius.baner.update',
            'attr' => array(
                'class' => 'btn btn-primary btn-lg'
            )
        ));

        return $form;
    }
    /**
     * Edits an existing Baner entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('Sylius\Bundle\CoreBundle\Model\Baner')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Baner не найден');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            if($entity->getFile()){
                if(file_exists($entity->getWebPath())){
                    unlink($entity->getWebPath());
                }
            }

            $em->flush();

            return $this->redirect($this->generateUrl('sylius_backend_baner_edit', array('id' => $id)));
        }

        return $this->render('SyliusWebBundle:Backend/Baner:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Baner entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('Sylius\Bundle\CoreBundle\Model\Baner')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Baner не найден');
            }

            if(file_exists($entity->getWebPath())){
                unlink($entity->getWebPath());
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('sylius_backend_baner_index'));
    }

    /**
     * Creates a form to delete a Baner entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('sylius_backend_baner_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array(
                'label' => 'sylius.baner.delete',
                'translation_domain' => 'messages',
                'attr' => array(
                    'class' => 'btn btn-danger btn-lg'
                )
            ))
            ->getForm()
        ;
    }
}
