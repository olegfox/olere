<?php

namespace Sylius\Bundle\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sylius\Bundle\CoreBundle\Model\News;
use Sylius\Bundle\CoreBundle\Model\NewsImages;
use Sylius\Bundle\CoreBundle\Model\NewsVideo;
use Sylius\Bundle\CoreBundle\Form\Type\NewsType;
use Sylius\Bundle\CoreBundle\Controller\VideoParser;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

class NewsController extends Controller
{
    public function indexAction()
    {
        $repository = $this->getDoctrine()
            ->getRepository('Sylius\Bundle\CoreBundle\Model\News');
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

                $em->persist($news);
                $em->flush();

                if($form["videoFile"]->getData() != ""){
                    $video = VideoParser::getVideo($form["videoFile"]->getData());
                    $v = new NewsVideo();
                    $v->setTitle($video->title);
                    $v->setLink($form["videoFile"]->getData());
                    $v->setContent($video->html);
                    $v->setNews($news);
                    $v->setThumbnail($video->thumbnail_url);
                    $em->persist($v);
                    $em->flush();
                }

                $imagesJson = $request->get('gallery');
                if ($imagesJson != "") {
                    $images = json_decode($imagesJson);
                    foreach ($images as $image) {
                        if ($image != "") {
                            $i = new NewsImages();
                            $i->setPath($image);
                            $i->setNews($news);
                            print $image;
                            $em->persist($i);
                            $em->flush();
                        }
                    }
                }

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
            ->getRepository('Sylius\Bundle\CoreBundle\Model\News');
        $news = $repository->find($id);
        $form = $this->createForm(new NewsType(), $news);

        if(is_object($news->getVideo()[0])){
            $form["videoFile"]->setData($news->getVideo()[0]->getLink());
        }

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){
                $em = $this->getDoctrine()->getManager();

                $imagesJson = $request->get('gallery');
                if ($imagesJson != "") {
                    $images = json_decode($imagesJson);
                    foreach ($images as $image) {
                        if ($image != "") {
                            $i = new NewsImages();
                            $i->setPath($image);
                            $i->setNews($news);
                            $em->persist($i);
                            $em->flush();
                        }
                    }
                }

                if($form["videoFile"]->getData() != ""){
                    $video = VideoParser::getVideo($form["videoFile"]->getData());
                    if(!is_object($news->getVideo()[0])){
                        $v = new NewsVideo();
                    }else{
                        $v = $news->getVideo()[0];
                    }
                    $v->setTitle($video->title);
                    $v->setLink($form["videoFile"]->getData());
                    $v->setContent($video->html);
                    $v->setThumbnail($video->thumbnail_url);
                    $v->setNews($news);
                    $em->persist($v);
                    $em->flush();
                }

                $em->flush();
                return $this->redirect($this->generateUrl('sylius_backend_news_index'));
            }
        }
        $params = array(
            'form' => $form->createView(),
            'news' => $news
        );
        return $this->render('SyliusWebBundle:Backend/News:update.html.twig', $params);
    }

    public function deleteAction($id){
        $repository = $this->getDoctrine()
            ->getRepository('Sylius\Bundle\CoreBundle\Model\News');
        $news = $repository->find($id);
        if($news){
            $em = $this->getDoctrine()->getManager();
            $em->remove($news);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('sylius_backend_news_index'));
    }

    public function imagesDeleteAction($id){
        $repository = $this->getDoctrine()
            ->getRepository('Sylius\Bundle\CoreBundle\Model\NewsImages');
        $newsImage = $repository->find($id);
        if($newsImage){

            if(file_exists('/images/news/' . $newsImage->getPath())){
                unlink('/images/news/' . $newsImage->getPath());
            }

            $em = $this->getDoctrine()->getManager();
            $em->remove($newsImage);
            $em->flush();

            return new Response('ok');
        }

        return new Response('no');
    }

    public function frontendIndexAction()
    {
        $repository = $this->getDoctrine()->getRepository('Sylius\Bundle\CoreBundle\Model\News');

        $news = $repository->findAll();

        $params = array(
            "news" => $news
        );
        return $this->render('SyliusWebBundle:Frontend/News:index.html.twig', $params);
    }

    public function frontendShowAction($slug){
        $repository = $this->getDoctrine()->getRepository('Sylius\Bundle\CoreBundle\Model\News');
        $n = $repository->findOneBy(array('slug' => $slug));

        $params = array(
            "n" => $n,
        );
        return $this->render('SyliusWebBundle:Frontend/News:one.html.twig', $params);
    }
}
