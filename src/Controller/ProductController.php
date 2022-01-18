<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ProductType;
use App\Entity\Product;

/**
 * @Route("/product")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="product_index")
     */
    public function index(): Response
    {
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }


    /**
     * @Route("/form", name="product_form")
     */
    public function form(Request $request): Response
    {        
        return $this->render('product/form.html.twig', [
            'formContent' => $this->createForm(ProductType::class)->$form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="product_new")
     */
    public function new(Request $request): Response
    {        
        return $this->render('product/form.html.twig', [
            'formContent' => $this->createForm(ProductType::class)->$form->createView(),
        ]);
    }
}
