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
        set_time_limit(0);
        ini_set('error_reporting', E_ALL);
        ini_set('display_errors', TRUE);
        header('Content-Type: text/html; charset=UTF-8');
        $import = new Import();

        $form = $this->createForm(new ImportType(), $import);
        $repository = $this->container->get('sylius.repository.product');
        $manager = $this->container->get('sylius.manager.product');
        if ($request->isMethod('POST')) {
            $form->bind($request);
            $xls = $form['file']->getData();
            if ($xls != "") {
//                $newFileName = uniqid() . '.' . 'xlsx';
                $newFileName = $xls->getClientOriginalName();
                $newFileName = iconv("utf-8", "cp1251", $newFileName);
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
                    if ($description == NULL) {
                        $description = "";
                    }
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
                        if (strlen($name) > 10) {
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
                        $product->setCatalog($catalog);
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
                        $manager->flush();
                        $product->setPosition($product->getId());
                        $product->setPosition2($product->getId());
                        $i++;
                    }
                }
                $manager->flush();
//                $this->importScanAction($request);
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

    public function scanCodeArticulAction()
    {
        set_time_limit(0);
        ini_set('error_reporting', E_ALL);
        ini_set('display_errors', TRUE);
        header('Content-Type: text/html; charset=UTF-8');
        $manager = $this->container->get('sylius.manager.product');
        $repositoryProducts = $this->container->get('sylius.repository.product');
        $products = $repositoryProducts->findAll();
        $count = 0;
        foreach ($products as $p) {
            if ($p->getSkuCode() == 4) {
//                print ($p->getCreatedAt()->getTimestamp() + 60*60*24*7*2)." = ".time();
                if ($p->getCreatedAt()->getTimestamp() + 60 * 60 * 24 * 7 * 2 < time()) {
                    $manager->remove($p);
                    $count++;
                }
            }
        }
        $manager->flush();
        return new Response("Удалено $count продуктов.");
    }

    public function scanProductsAction(Request $request)
    {
        set_time_limit(0);
        ini_set('error_reporting', E_ALL);
        ini_set('display_errors', TRUE);
        header('Content-Type: text/html; charset=UTF-8');
        $repositoryProducts = $this->container->get('sylius.repository.product');
        $products = $repositoryProducts->findAll();
//        $em = $this->getDoctrine()->getManager();
//        $products = $em->createQuery(
//            'SELECT p FROM
//             Sylius\Bundle\CoreBundle\Model\Product p
//             WHERE p.taxons IS NULL
//            '
//        )->getResult();
        $count = 0;
        if (count($products) > 0) {
            $repositoryTaxon = $this->container->get('sylius.repository.taxon');
            $manager = $this->container->get('sylius.manager.product');
            foreach ($products as $product) {
                $ctf = 0;
                $cl = 0;
                $taxons = new ArrayCollection();
                $ar = array();
                $ar['ctf'] = $ctf;
                $ar['cl'] = $cl;
                foreach ($product->getTaxons() as $i => $taxon) {
                    $ar['taxon_id' . $i] = $taxon->getId();
                    if ($taxon->getTaxonomy()->getId() == 8) {
                        $ctf = 1;
                        $ar['ctf'] = $ctf;
                        $ar['catalogParent'] = $taxon->getTaxonomy()->getId();
                    }
                    if ($taxon->getTaxonomy()->getId() == 9) {
                        $cl = 1;
                        $ar['cl'] = $cl;
                        $ar['collectionParent'] = $taxon->getTaxonomy()->getId();
                    }
                }
                if ($ctf == 0) {
                    $ar['catalog'] = $product->getCatalog();
                    $catalog = $repositoryTaxon->findOneBy(array('name' => $product->getCatalog()));
                    if (is_object($catalog)) {
                        $taxons[] = $catalog;
                    }
                }
                if ($cl == 0) {
                    $ar['collection'] = $product->getCollection();
                    $collection = $repositoryTaxon->findOneBy(array('name' => $product->getCollection()));

                    if (is_object($collection)) {
                        $taxons[] = $collection;
                    }
                }
                if (count($taxons) > 0) {
                    print $product->getName();
                    $ar['product'] = $product->getId();
                    foreach ($taxons as $t) {
                        $fl = 0;
                        foreach ($product->getTaxons() as $tt) {
                            if ($tt->getId() == $t->getId()) {
                                $fl = 1;
                            }
                        }
                        if ($fl == 0) {
                            $product->addTaxon($t);
                        }
                    }
                    $manager->flush();
                    print json_encode($ar);
                    $count++;
                }
            }
        }
        return new Response("Обновлено $count продуктов.");
    }

    public function importScanAction(Request $request)
    {
        set_time_limit(0);
        ini_set('error_reporting', E_ALL);
        ini_set('display_errors', TRUE);
        header('Content-Type: text/html; charset=UTF-8');
        $repository = $this->container->get('sylius.repository.product');
        $manager = $this->container->get('sylius.manager.product');
//        if ($request->isMethod('POST')) {
        $products = $repository->findAll();
        $connect = ftp_connect('fotobank.olere.ru');
        $adapter = new LocalAdapter($this->get('kernel')->getRootDir() . '/../web/media/image');
        $filesystem = new Filesystem($adapter);
        $imageUploader = new ImageUploader($filesystem);

        if (!$connect) {
            return false;
        }

        $result = ftp_login($connect, 'anonymous', '');

        if ($result == false) return false;
        ftp_set_option($connect, FTP_TIMEOUT_SEC, 80000);
        ftp_pasv($connect, true);
        if ($result) {
            $count = 0;
            $total = 0;
//            ftp_chdir($connect, '/');
            $files = ftp_nlist($connect, ".");
            foreach ($products as $p) {
                if (is_object($p)) {
                    $sku = $p->getSku();
//                Scan ftp for sku
                    $images = array();
//                print "sku = ".$sku;
                    foreach ($files as $file) {
                        $fileName = str_replace('./', '', $file);
                        if (@stristr($fileName, $sku) === false || @stripos($fileName, $sku) != 0) {

                        } else {
                            $fl = 0;
                            foreach ($images as $i) {
                                if ($i == $fileName) {
                                    $fl = 1;
                                }
                            }
                            foreach ($p->getMasterVariant()->getImages() as $image) {
                                $path = $image->getOriginal();
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
                    if (count($images) > 0) {
                        natcasesort($images);
                        $images = array_reverse($images);
                        foreach ($images as $i) {
                            if (ftp_get($connect, $_SERVER['DOCUMENT_ROOT'] . '/import/files/' . $i, $i, FTP_BINARY, 0)) {
                                $variantImage = new VariantImage();
                                $fileinfo = new \SplFileInfo(getcwd() . '/import/files/' . $i);
                                $variantImage->setFile($fileinfo);
                                $imageUploader->upload($variantImage);
                                $variantImage->setOriginal($i);
                                $p->getMasterVariant()->addImage($variantImage);
                                $manager->flush();
                                $count++;
                            } else {
                                print("Не удалось скачать файл " . $i . "\n");
                            }
                            $total++;
                        }
                    }
                }
            }
            ftp_quit($connect);
            return new Response("Необходимо обновить " . $total . ".Обновление картинок завершено. Обновлено " . $count . " картинок.");
        }
//        }

        return new Response("fail");
    }

    public function removeDoubleAction()
    {
        $em = $this->getDoctrine()->getManager();

//        $products = $em->createQuery(
//            'SELECT v FROM
//             Sylius\Bundle\CoreBundle\Model\Product v
//            '
//        )->getResult();
//        $count = 0;
//        foreach($products as $p){
//            if(!is_object($p->getMasterVariant())){
//                $em->remove($p);
//                $count++;
//            }
//        }
//        $em->flush();
//        return new Response($count);
        $variant = $em->createQuery(
            'SELECT v FROM
             Sylius\Bundle\CoreBundle\Model\Variant v
            '
        )->getResult();
        $deleted = array();
        $deletedProduct = array();
        foreach ($variant as $v) {
            $fl = 0;
            foreach ($deleted as $del) {
                if ($del == $v->getId()) {
                    $fl = 1;
                    break;
                }
            }
            if ($fl == 0) {
                foreach ($variant as $d) {
                    if ($v->getSku() == $d->getSku() && $v->getProduct()->getId() != $d->getProduct()->getId()) {
                        $deleted[] = $d->getId();
                        $deletedProduct[] = $d->getProduct()->getId();
                        $em->remove($d->getProduct());
                        $em->remove($d);
                    }
                }
            }
        }
        $em->flush();
        return new Response(json_encode($deletedProduct));
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
    public
    function indexByTaxonAction(Request $request, $page, $category)
    {
        $routeName = $request->get('_route');
        $price = "any";
        $type = 0;
//        if($this->container->get('security.context')->isGranted('ROLE_USER_OPT')){
        $type = 1;
//        }
        if ($request->get('price') != null) {
            $price = $request->get('price');
        }
        $em = $this->getDoctrine()->getManager();
        if ($routeName == 'sylius_product_index_by_taxon') {

            $taxons = $em->createQuery(
                'SELECT t FROM
                 Sylius\Bundle\CoreBundle\Model\Taxon t
                 WHERE t.taxonomy = :taxonomy AND t.parent IS NOT NULL
                '
            )->setParameter('taxonomy', 8)->getResult();
        } else {
            $taxons = $em->createQuery(
                'SELECT t FROM
                 Sylius\Bundle\CoreBundle\Model\Taxon t
                 WHERE t.taxonomy = :taxonomy AND t.parent IS NOT NULL
                '
            )->setParameter('taxonomy', 9)->getResult();
        }


        $sorting = array(
            "position" => 'ASC'
        );

        $taxon = $this->get('sylius.repository.taxon')
            ->findOneBySlug($category);

        $groups = $this->get('sylius.repository.group')->findAll();

        if (count($taxon->getChildren()) > 0) {
            $collections = $this->get('sylius.repository.taxon')->findBy(array("parent" => $taxon->getId()));
            $paginator = $this
                ->getRepository()
                ->createByTaxonPaginator($taxon, $sorting, $price, $type);

            $paginator->setMaxPerPage(40);
            $paginator->setCurrentPage($request->query->get('page', $page));

            return $this->render('SyliusWebBundle:Frontend/Taxon:sub_collection.html.twig', array(
                'collections' => $collections,
                'taxon' => $taxon,
                'products' => $paginator,
                'groups' => $groups,
                'price' => $price,
                'taxons' => $taxons
            ));
        }

        if (!isset($taxon)) {
            throw new NotFoundHttpException('Requested taxon does not exist.');
        }

        if ($page != 'all') {
            $paginator = $this
                ->getRepository()
                ->createByTaxonPaginator($taxon, $sorting, $price, $type);

            $paginator->setMaxPerPage(40);
            $paginator->setCurrentPage($request->query->get('page', $page));
        } else {
            $paginator = $taxon->getProducts();
        }

        return $this->render($this->config->getTemplate('indexByTaxon.html'), array(
            'taxon' => $taxon,
            'products' => $paginator,
            'permalink' => "/catalog/" . $page . "/" . $category,
            'groups' => $groups,
            'page' => $page,
            'taxons' => $taxons,
            'price' => $price
        ));
    }

    public
    function indexByTaxonProductAction(Request $request, $page, $category, $subcategory, $slug)
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

        $paginator->setMaxPerPage(40);
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

    public
    function indexByTaxonIdAction(Request $request, $id)
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
    public
    function historyAction(Request $request)
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
    public
    function filterFormAction(Request $request)
    {
        return $this->render('SyliusWebBundle:Backend/Product:filterForm.html.twig', array(
            'form' => $this->get('form.factory')->createNamed('criteria', 'sylius_product_filter', $request->query->get('criteria'))->createView()
        ));
    }

    public
    function showFrontendAction(Request $request, $taxon)
    {
        $groups = $this->get('sylius.repository.group')->findAll();
        $product = $this->get('sylius.repository.product')->findOneBy(array("slug" => $request->get('slug')));
        $taxon = $this->get('sylius.repository.taxon')->findOneBy(array("id" => $taxon));
        return $this->render($this->config->getTemplate('show.html.twig'), array(
            'product' => $product,
            'groups' => $groups,
            'taxon' => $taxon
        ));
    }

    public
    function childrenActiveAction(Request $request)
    {
        $idParent = $request->get("parent");
        $idChild = $request->get("child");
        if ($idParent != '' && $idChild != '') {
            $parent = $this->get('sylius.repository.product')->findOneById($idParent);
            $child = $this->get('sylius.repository.product')->findOneById($idChild);
            if ($parent && $child) {
                $manager = $this->container->get('sylius.manager.product');
                if ($request->get("active") == 1) {
                    $parent->addChildren($child);
                } else {
                    $parent->removeChildren($child);
                }
                $manager->flush();
                return new Response("ok");
            }
        }
        return new Response("no");
    }

    public
    function childrenAction(Request $request, $parent)
    {
        $product = $this->get('sylius.repository.product')->findOneById($parent);
        $taxons = $this->get('sylius.repository.taxon')->findAll();
        return $this->render('SyliusWebBundle:Backend/Product:children.html.twig', array(
            'children' => $product->getChildren(),
            'parent' => $parent,
            'taxons' => $taxons
        ));
    }

    public
    function productsTaxonAction(Request $request, $taxon, $parent)
    {
        $taxon = $this->get('sylius.repository.taxon')->findOneById($taxon);
        return $this->render('SyliusWebBundle:Backend/Product:productsTaxon.html.twig', array(
            'products' => $taxon->getProducts(),
            'parent' => $parent
        ));
    }

    public
    function deleteAllAction(Request $request)
    {
        $idx = $request->get('idx_all');
        $repository = $this->container->get('sylius.repository.product');
        $manager = $this->container->get('sylius.manager.product');
        if ($idx) {
            $idx = json_decode($idx);
            if (count($idx) > 0) {
                foreach ($idx as $id) {
                    $product = $repository->findOneBy(array("id" => $id));
                    $manager->remove($product);
                    $manager->flush();
                }
            }
        }
        return $this->redirectHandler->redirectToReferer();
    }

    public
    function editGroupAction(Request $request)
    {
        $id = $request->get('id');
        if ($id) {
            $name = $request->get('name');
            $sku = $request->get('sku');
            $price = $request->get('price');
            $priceOpt = $request->get('priceOpt');

            $repository = $this->container->get('sylius.repository.product');
            $manager = $this->container->get('sylius.manager.product');

            $product = $repository->findOneBy(array("id" => $id));

            if ($product) {

                if ($name != '') {
                    $product->setName($name);
                }
                if ($sku != '') {
                    $product->setSku($sku);
                }
                if ($price != '') {
                    $product->setPrice($price * 100);
                }
                if ($priceOpt != '') {
                    $product->setPriceOpt($priceOpt * 100);
                }

                $manager->flush();
                return new Response("ok");
            }
        }

        return new Response("no");
    }

    public
    function editProductsAction(Request $request)
    {
        if ($request->isMethod('POST')) {
            $ids = $request->get('ids');
            $ids = json_decode($ids);
            $manager = $this->container->get('sylius.manager.product');
            if (count($ids) > 0) {
                $skuCode = $request->get('skuCode');
                $enabled = $request->get('enabled');
                $taxons = $request->get('taxons');

                $products = $manager->createQuery(
                    'SELECT p FROM
                     Sylius\Bundle\CoreBundle\Model\Product p
                     WHERE p.id IN (:ids)
                    '
                )->setParameter('ids', $ids)->getResult();

                if (count($products) > 0) {
                    foreach ($products as $p) {
                        if ($skuCode != null) {
                            $p->setSkuCode($skuCode);
                        }
                        if ($enabled != null) {
                            $p->setEnabled($enabled);
                        }
                        if (count($taxons) > 0) {
                            $taxs = $manager->createQuery(
                                'SELECT t FROM
                                 Sylius\Bundle\CoreBundle\Model\Taxon t
                                 WHERE t.id IN (:ids)
                                '
                            )->setParameter('ids', $taxons)->getResult();

                            if (count($taxs) > 0) {
                                $txs = new ArrayCollection();
                                foreach ($taxs as $t) {
                                    $txs[] = $t;
                                }
                                $p->setTaxons($txs);
                            }
                        }

                    }


                    $manager->flush();
                    return new Response("ok");
                }
            }

            return new Response("no");
        }
        $repository = $this->container->get('sylius.repository.taxon');
        $taxons = $repository->findAll();
        return $this->render('SyliusWebBundle:Backend/Product:groupForm.html.twig', array(
            'taxons' => $taxons
        ));
    }

//    public function deleteAction(Request $request){
//        $repository = $this->container->get('sylius.repository.product');
//        $manager = $this->container->get('sylius.manager.product');
//        $product = $repository->findOneBy(array('id' => $request->get('id')));
//        $manager->remove($product);
//        $manager->flush();
//    }

    public function getPriceAction($id, $type, $taxon){
        $repositoryProduct = $this->container->get('sylius.repository.product');
        $repositorySale = $this->getDoctrine()
            ->getRepository('Sylius\Bundle\CoreBundle\Model\Sale');
        $product = $repositoryProduct->find($id);
        $price = 0;
        $priceOld = 0;
        if($type == 0){
            $price = $product->getPrice();
            $priceOld = $price;
        }else{
            $price = $product->getPriceOpt();
            $priceOld = $price;
        }
        $sale = $repositorySale->findOneBy(array('taxonId' => $taxon));
        if($sale){
            if($type == $sale->getTypePrice()){
                $price = $price - $price*$sale->getPercent()/100;
            }
        }
        return $this->render('SyliusWebBundle:Frontend/Product:getPrice.html.twig', array(
            'price' => $price,
            'priceOld' => $priceOld
        ));
    }
}
