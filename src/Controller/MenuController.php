<?php

namespace App\Controller;

use App\Repository\ProductCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{
    public function index(ProductCategoryRepository $categoryRepository)
    {
        return $this->render('menu/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }
}
