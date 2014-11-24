<?php

namespace Sylius\Bundle\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sylius\Bundle\CoreBundle\Model\News;
use Sylius\Bundle\CoreBundle\Model\NewsImages;
use Sylius\Bundle\CoreBundle\Model\NewsVideo;
use Sylius\Bundle\CoreBundle\Form\Type\NewsType;
use Sylius\Bundle\CoreBundle\Controller\VideoParser;

class NewsController extends Controller
{
    public function indexAction()
    {
        $repository = $this->getDoctrine()
            ->getRepository('SyliusWebBundle:News');
        $news = $repository->findAll();
        $params = array(
            'news' => $news
        );
        return $this->render('SyliusWebBundle:Backend/News:index.html.twig', $params);
    }

    public function createAction(Request $request){
        $news = new News();
        $form = $this->createForm(new NewsType(), $news);
        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){

                $em = $this->getDoctrine()->getManager();

                if($form["video"]->getData() != ""){
                    $video = VideoParser::getVideo($form["video"]->getData());
                    $v = new NewsVideo();
                    $v->setTitle($video->title);
                    $v->setLink($form["video"]->getData());
                    $v->setContent($video->html);
                    $em->persist($v);
                    $em->flush();
                    $news->addVideo($v);
                }

                $em->persist($news);
                $em->flush();

                $imagesJson = $request->get('gallery');
                if ($imagesJson != "") {
                    $images = json_decode($imagesJson);
                    foreach ($images as $image) {
                        if ($image != "") {
                            $i = new NewsImages();
                            $i->setPath($image);
                            $em->persist($i);
                            $em->flush();
                            $news->addImage($i);
                        }
                    }
                }

                $em->flush();
                return $this->redirect($this->generateUrl('sylius_backend_news_index'));
            }
        }
        $params = array(
            'form' => $form->createView()
        );
        return $this->render('SyliusWebBundle:Backend/News:create.html.twig', $params);
    }

    public function updateAction(Request $request, $id){
        $repository = $this->getDoctrine()
            ->getRepository('SiteMainBundle:News');
        $news = $repository->find($id);
        $form = $this->createForm(new NewsType(), $news);
        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){
                $em = $this->getDoctrine()->getManager();

//              Загрузка фото-превью
                if($news->getFilePreview() !== null){

                    if($news->getPreview() === null){
                        $i = new Images();
                    }else{
                        $i = $news->getPreview();
                    }

                    $i->setFile($news->getFilePreview());
                    $i->upload();

                    if($news->getPreview() === null){
                        $em->persist($i);
                    }

                    $em->flush();
                    $news->setPreview($i);
                }

//              Загрузка фото-шапки
                if($news->getFileHeaderPhoto() !== null){

                    if($news->getHeaderPhoto() === null){
                        $i = new Images();
                    }else{
                        $i = $news->getHeaderPhoto();
                    }

                    $i->setFile($news->getFileHeaderPhoto());
                    $i->upload();

                    if($news->getHeaderPhoto() === null){
                        $em->persist($i);
                    }

                    $em->flush();
                    $news->setHeaderPhoto($i);
                }

                $em->flush();
                return $this->redirect($this->generateUrl('backend_news_index'));
            }
        }
        $params = array(
            'form' => $form->createView(),
            'news' => $news
        );
        return $this->render('SiteMainBundle:Backend/News:update.html.twig', $params);
    }

    public function deleteAction($id){
        $repository = $this->getDoctrine()
            ->getRepository('SiteMainBundle:News');
        $news = $repository->find($id);
        if($news){
            $em = $this->getDoctrine()->getManager();
            $em->remove($news);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('backend_news_index'));
    }
}
