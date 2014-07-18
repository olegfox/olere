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

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sylius\Bundle\ProductBundle\Model\Import;
use Sylius\Bundle\ProductBundle\Form\Type\ImportType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sylius\Bundle\CoreBundle\Model\VariantImage;
use Sylius\Bundle\CoreBundle\Uploader\ImageUploader;
use Gaufrette\Filesystem;
use Gaufrette\Adapter\Local as LocalAdapter;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Product controller.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ProductController extends ResourceController
{

    public function collectionAction(Request $request)
    {
        $collections = $this->get('sylius.repository.taxonomy')->findBy(array("id" => 8));
//        $collections = array();
//        foreach($parent as $p){
//            foreach($p->getTaxons() as $t){
//                $collections[] = $t;
//            }
//        }
        return $this->render('SyliusWebBundle:Frontend/Taxon:collection.html.twig', array(
            'collections' => $collections
        ));
    }

    public function collectionsAction(Request $request)
    {
        $collections = $this->get('sylius.repository.taxonomy')->findBy(array("id" => 9));
//        $collections = array();
//        foreach($parent as $p){
//            foreach($p->getTaxons() as $t){
//                $collections[] = $t;
//            }
//        }
        return $this->render('SyliusWebBundle:Frontend/Taxon:collection.html.twig', array(
            'collections' => $collections
        ));
    }

    public function importIndexAction(Request $request)
    {
        header('Content-Type: text/html; charset=UTF-8');
        $import = new Import();

        $form = $this->createForm(new ImportType(), $import);
        $repository = $this->container->get('sylius.repository.product');
        $manager = $this->container->get('sylius.manager.product');
        if ($request->isMethod('POST')) {
            $form->bind($request);
            $xls = $form['file']->getData();
            if ($xls != "") {
                $newFileName = uniqid() . '.' . 'xlsx';
                $xls->move('import/xls/', $newFileName);
                $objPHPExcel = $this->get('phpexcel')->createPHPExcelObject('import/xls/' . $newFileName);
                $objWorksheet = $objPHPExcel->getActiveSheet();
                $highestRow = $objWorksheet->getHighestRow();
//            foreach ($objWorksheet->getDrawingCollection() as $drawing) {
//                    print "imageContents = ".$drawing->getPath()."<br/>";
//            }
                $data = array();
                $i = 0;
                $imagesJson = $request->get('gallery');
                $adapter = new LocalAdapter($this->get('kernel')->getRootDir() . '/../web/media/image');
                $filesystem = new Filesystem($adapter);
                $imageUploader = new ImageUploader($filesystem);
                for ($row = 2; $row <= $highestRow; $row++) {
                    $articul = $objPHPExcel->getActiveSheet()->getCell('A' . $row)->getValue();
                    $name = $objPHPExcel->getActiveSheet()->getCell('B' . $row)->getValue();
                    $gb = $objPHPExcel->getActiveSheet()->getCell('C' . $row)->getValue();
                    $color = $objPHPExcel->getActiveSheet()->getCell('D' . $row)->getValue();
                    $sost = $objPHPExcel->getActiveSheet()->getCell('E' . $row)->getValue();
                    $description = $objPHPExcel->getActiveSheet()->getCell('F' . $row)->getValue();
                    $collection = $objPHPExcel->getActiveSheet()->getCell('G' . $row)->getValue();
                    $catalog = $objPHPExcel->getActiveSheet()->getCell('H' . $row)->getValue();
                    $codeArticul = $objPHPExcel->getActiveSheet()->getCell('I' . $row)->getValue();
                    $priceOpt = $objPHPExcel->getActiveSheet()->getCell('J' . $row)->getValue();
                    $price = $objPHPExcel->getActiveSheet()->getCell('K' . $row)->getValue();
                    $data[$i] = array(
                        "articul" => $articul,
                        "name" => $name,
                        "sost" => $sost,
                        "color" => $color,
                        "gb" => $gb,
                        "price" => $price,
                        "description" => $description,
                        "collection" => $collection,
                        "codeArticul" => $codeArticul,
                        "priceOpt" => $priceOpt,
                        "catalog" => $catalog,
                        "image" => ""
                    );
                    if ($name != "") {
//                        if ($imagesJson != "") {
//                            $images = json_decode($imagesJson);
//                            foreach ($images as $image) {
//                                $path_parts = explode(".", $image);
//                                if (@stristr($path_parts[0], $articul) === false) {
//
//                                } else {
//                                    $data[$i]["image"][] = $image;
//                                }
//                            }
//                        }
                        $nameCat = $name;
                        if(strlen($name) > 10){
                            $nameCat = mb_substr($name, 0, 10);
                        }
//                        print 'Сокращённое название каталога: '.$nameCat;
                        $cat = $this->get('sylius.repository.taxon')->findOneBy(array("name" => $catalog));
//                    Находим в базе коллекцию по названию из таблицы
                        $col = $this->get('sylius.repository.taxon')->findOneBy(array("name" => $collection));
                        $taxs = new ArrayCollection();
                        if ($col || $cat) {
                            if ($cat) {
                                $taxs[] = $cat;
                            }
                            if ($col) {
                                $taxs[] = $col;
                            }
                        }
                        $product = $repository->createNew();
                        $product->setCatalog($name);
                        $product->setCollection($collection);
                        $product->setName($name);
                        $product->setDescription($description);
                        $product->setPrice($price * 100);
                        $product->setPriceOpt($priceOpt * 100);
                        if (count($taxs) > 0) {
                            $product->setTaxons($taxs);
                        }
                        $product->getMasterVariant()->setSku($articul);
                        $product->getMasterVariant()->setSkuCode($codeArticul);


                        /* Add product property */
                        $propertyRepository = $this->container->get('sylius.repository.property');
                        $productPropertyRepository = $this->container->get('sylius.repository.product_property');

                        /* Color property */
                        $color_property = $propertyRepository->findOneBy(array('id' => 10));
                        $productProperty = $productPropertyRepository->createNew();

                        $productProperty
                            ->setProperty($color_property)
                            ->setValue($color);

                        $product->addProperty($productProperty);

                        /* end Color property */

                        /* Gb property */
                        $gb_property = $propertyRepository->findOneBy(array('id' => 11));
                        $productProperty = $productPropertyRepository->createNew();

                        $productProperty
                            ->setProperty($gb_property)
                            ->setValue($gb);

                        $product->addProperty($productProperty);

                        /* end Gb property */

                        /* Sost property */
                        $sost_property = $propertyRepository->findOneBy(array('id' => 12));
                        $productProperty = $productPropertyRepository->createNew();

                        $productProperty
                            ->setProperty($sost_property)
                            ->setValue($sost);

                        $product->addProperty($productProperty);

                        /* end Gb property */

                        /* end Add product property */


//                        if (isset($data[$i]["image"][0])) {
//                            if ($data[$i]["image"][0] != "") {
//                                foreach ($data[$i]["image"] as $im) {
//                                    $variantImage = new VariantImage();
//                                    $fileinfo = new \SplFileInfo(getcwd() . '/import/files/' . $im);
//                                    $variantImage->setFile($fileinfo);
//                                    $imageUploader->upload($variantImage);
//                                    $product->getMasterVariant()->addImage($variantImage);
//                                }
//                            }
//                        }
                        $product->getMasterVariant()->setOnHand(1);
                        $manager->persist($product);
                        $i++;
                    }
                }
                $manager->flush();
                $this->importScanAction($request);
                return $this->render('SyliusWebBundle:Backend/Import:index.html.twig', array(
                    'form' => $form->createView(),
                    'data' => $data
                ));
            }
        }

        return $this->render('SyliusWebBundle:Backend/Import:index.html.twig', array(
            'form' => $form->createView()
        ));
    }

    private function uploadFTP($name, $NewName, $login, $pass, $host, $path)
    {


    }

    public function importScanAction(Request $request)
    {
        header('Content-Type: text/html; charset=UTF-8');
        $repository = $this->container->get('sylius.repository.product');
        $manager = $this->container->get('sylius.manager.product');
//        if ($request->isMethod('POST')) {
        $products = $repository->findAll();
        $connect = ftp_connect('fotobank.olere.ru');

        if (!$connect) {
            return false;
        }

        $result = ftp_login($connect, 'anonymous', '');

        if ($result == false) return false;

        if ($result) {
            $count = 0;
//            ftp_chdir($connect, '/');
            $files = ftp_nlist($connect, ".");
            foreach ($products as $p) {
                $sku = $p->getSku();
//                Scan ftp for sku
                $images = array();
//                print "sku = ".$sku;
                foreach ($files as $file) {
                    $fileName = str_replace('./', '', $file);
                    if (@stristr($fileName, $sku) === false) {

                    } else {
                        $fl = 0;
                        foreach ($images as $i) {
                            if ($i == $fileName) {
                                $fl = 1;
                            }
                        }
                        foreach ($p->getMasterVariant()->getImages() as $image) {
                            $path = $image->getPath();
//                            $path_parts = explode(".", $path);
                            if ($path == $fileName) {
                                $fl = 1;
                            }
                        }
//                        print "fl = ".$fl;
                        if ($fl == 0) {
                            $images[] = $fileName;
                        }
                    }
                }
//                print var_dump($files);
                if(count($images) > 0){
                    natcasesort($images);
                    $images=array_reverse($images);
                    foreach ($images as $i) {
                        $variantImage = new VariantImage();
                        $variantImage->setPath($i);
                        $p->getMasterVariant()->addImage($variantImage);
                        $manager->flush();
                        $count++;
                    }
                }
            }
            ftp_quit($connect);
            return new Response("Обновление картинок завершено. Обновлено " . $count . " картинок.");
        }
//        }

        return new Response("fail");
    }

    /**
     * List products categorized under given taxon.
     *
     * @param Request $request
     * @param string $permalink
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function indexByTaxonAction(Request $request, $page, $category)
    {
        $sorting = array(
            "position" => 'ASC'
        );

        $taxon = $this->get('sylius.repository.taxon')
            ->findOneBySlug($category);

        $groups = $this->get('sylius.repository.group')->findAll();

        if(count($taxon->getChildren()) > 0){
            $collections = $this->get('sylius.repository.taxon')->findBy(array("parent" => $taxon->getId()));
            $paginator = $this
                ->getRepository()
                ->createByTaxonPaginator($taxon, $sorting);

            $paginator->setMaxPerPage(30);
            $paginator->setCurrentPage($request->query->get('page', $page));

            return $this->render('SyliusWebBundle:Frontend/Taxon:sub_collection.html.twig', array(
                'collections' => $collections,
                'taxon' => $taxon,
                'products' => $paginator,
                'groups' => $groups
            ));
        }

        if (!isset($taxon)) {
            throw new NotFoundHttpException('Requested taxon does not exist.');
        }

        $paginator = $this
            ->getRepository()
            ->createByTaxonPaginator($taxon, $sorting);

        $paginator->setMaxPerPage(30);
        $paginator->setCurrentPage($request->query->get('page', $page));

        return $this->render($this->config->getTemplate('indexByTaxon.html'), array(
            'taxon' => $taxon,
            'products' => $paginator,
            'permalink' => "/catalog/" . $page . "/" . $category,
            'groups' => $groups
        ));
    }

    public function indexByTaxonProductAction(Request $request, $page, $category, $subcategory, $slug)
    {
        $taxon = $this->get('sylius.repository.taxon')
            ->findOneByPermalink($category . "/" . $subcategory);

        if (!isset($taxon)) {
            throw new NotFoundHttpException('Requested taxon does not exist.');
        }

        $sorting = array(
            "position" => 'ASC'
        );

        $paginator = $this
            ->getRepository()
            ->createByTaxonPaginator($taxon, $sorting);

        $paginator->setMaxPerPage(30);
        $paginator->setCurrentPage($request->query->get('page', $page));

        if ($slug != '') {
            $f = 0;
            foreach ($paginator as $p) {
                if ($p->getSlug() == $slug) {
                    $f = 1;
                }
            }
            if ($f == 0) {
                return $this->redirect($this->generateUrl('sylius_product_index_by_taxon', array("page" => $page, "category" => $category, "subcategory" => $subcategory)));
            }
        }

        return $this->render($this->config->getTemplate('indexByTaxon.html'), array(
            'taxon' => $taxon,
            'products' => $paginator,
            'slug' => $slug,
            'permalink' => "/catalog/" . $page . "/" . $category . "/" . $subcategory
        ));
    }

    public function indexByTaxonIdAction(Request $request, $id)
    {
        $taxon = $this->get('sylius.repository.taxon')->find($id);

        if (!isset($taxon)) {
            throw new NotFoundHttpException('Requested taxon does not exist.');
        }

        $sorting = array(
            "position" => 'ASC'
        );

        $paginator = $this
            ->getRepository()
            ->createByTaxonPaginator($taxon, $sorting);

        $paginator->setMaxPerPage($this->config->getPaginationMaxPerPage());
        $paginator->setCurrentPage($request->query->get('page', 1));

        return $this->render($this->config->getTemplate('productIndex.html'), array(
            'taxon' => $taxon,
            'products' => $paginator,
        ));
    }

    /**
     * Get product history changes.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function historyAction(Request $request)
    {
        $product = $this->findOr404($request);

        $logEntryRepository = $this->get('doctrine')->getManager()->getRepository('Gedmo\Loggable\Entity\LogEntry');

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('history.html'))
            ->setData(array(
                $this->config->getResourceName() => $product,
                'logs' => $logEntryRepository->getLogEntries($product)
            ));

        return $this->handleView($view);
    }

    /**
     * Render product filter form.
     *
     * @param Request $request
     */
    public function filterFormAction(Request $request)
    {
        return $this->render('SyliusWebBundle:Backend/Product:filterForm.html.twig', array(
            'form' => $this->get('form.factory')->createNamed('criteria', 'sylius_product_filter', $request->query->get('criteria'))->createView()
        ));
    }

    public function showAction(Request $request){
        $groups = $this->get('sylius.repository.group')->findAll();
        $product = $this->get('sylius.repository.product')->findOneBy(array("slug" => $request->get('slug')));
        return $this->render($this->config->getTemplate('show.html.twig'), array(
            'product' => $product,
            'groups' => $groups
        ));
    }

    public function deleteAllAction(Request $request){
        $idx = $request->get('idx_all');
        $repository = $this->container->get('sylius.repository.product');
        $manager = $this->container->get('sylius.manager.product');
        if($idx){
            $idx = json_decode($idx);
            if(count($idx) > 0){
                foreach($idx as $id){
                    $product = $repository->findOneBy(array("id" => $id));
                    $manager->remove($product);
                    $manager->flush();
                }
            }
        }
        return $this->redirectHandler->redirectToReferer();
    }

//    public function deleteAction(Request $request){
//        $repository = $this->container->get('sylius.repository.product');
//        $manager = $this->container->get('sylius.manager.product');
//        $product = $repository->findOneBy(array('id' => $request->get('id')));
//        $manager->remove($product);
//        $manager->flush();
//    }
}
