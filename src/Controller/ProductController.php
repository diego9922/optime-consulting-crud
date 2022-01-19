<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ProductType;
use App\Entity\Product;
use Knp\Component\Pager\PaginatorInterface;
use App\Handler\SpreadsheetHandler;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

/**
 * @Route("/")
 * @Route("/product")
 */
class ProductController extends AbstractController
{

    private $em;
    private $paginator;
    private $spreadsheetHandler;

    public function __construct(EntityManagerInterface $em, PaginatorInterface $paginator, SpreadsheetHandler $spreadsheetHandler){
        $this->em = $em;
        $this->paginator = $paginator;
        $this->spreadsheetHandler = $spreadsheetHandler;
    }

    /**
     * @Route("/new", name="product_new")
     */
    public function new(Request $request)
    {   
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        $message = "";
        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData();
            try {
                $this->em->persist($product);
                $this->em->flush();
                return $this->redirectToRoute('product_list');
            } catch (UniqueConstraintViolationException $e) {
                $message = "Error de valores unicos, verifique los valores ingresados";
            } catch (Exception $e){
                $message = "No fue posible guardar los datos";
            }
        }
        return $this->render('product/form.html.twig', [
            'formContent' => $this->createForm(ProductType::class)->createView(),
            'message' => $message
        ]);
    }

    /**
     * @Route("/", name="product_index")
     * @Route("/list", name="product_list")
     */
    public function list(Request $request): Response
    {
        $filterName = $request->get('filterField', null);
        $filterValue = $request->get('filterValue', null);

        $query = $this->em->getRepository(Product::class)->findLikeAndSort(
            $request->get('filterField', null), 
            $request->get('filterValue', null), 
            $request->get('sort', null), 
            $request->get('direction', null)
        );

        $pagination = $this->paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('product/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * @Route("/update/{productId}", name="product_update")
     */
    public function update($productId, Request $request)
    {
        $product = $this->em->getRepository(product::class)->find($productId);
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        $message = "";
        if ($form->isSubmitted() && $form->isValid()) {
            $productFormData = $form->getData();
            try {
                $product->setCode($productFormData->getCode());
                $product->setName($productFormData->getName());
                $product->setDescription($productFormData->getDescription());
                $product->setBrand($productFormData->getBrand());
                $product->setPrice($productFormData->getPrice());
                $product->setCategory($productFormData->getCategory());
                $this->em->persist($product);
                $this->em->flush();
                return $this->redirectToRoute('product_list');
            } catch (UniqueConstraintViolationException $e) {
                $message = "Error de valores unicos, verifique los valores ingresados";
            } catch (Exception $e){
                $message = "No fue posible guardar los datos";
            }
        }

        return $this->render('product/form.html.twig', [
            'formContent' => $form->createView(),
            'message' => $message
        ]);
    }

    /**
     * @Route("/delete/{productId}", name="product_delete")
     */
    public function delete($productId)
    {
        $this->em->remove($this->em->getRepository(product::class)->find($productId));
        $this->em->flush();
        return $this->redirectToRoute('product_list');
    }

    
    /**
     * @Route("/report/xlsx", name="product_report_xlsx")
     */
    public function reportXlsx()
    {
        return $this->file(
            $this->spreadsheetHandler->generateReportProduct(
                $this->em->getRepository(Product::class)->findAll()
            )
        );
    }
}
