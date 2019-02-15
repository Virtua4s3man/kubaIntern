<?php

namespace App\Controller;

use App\Repository\ProductCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MenuController extends AbstractController
{
    public function index(ProductCategoryRepository $categoryRepository)
    {
        return $this->render('menu/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }
}
