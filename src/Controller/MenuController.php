<?php

namespace App\Controller;

use App\Repository\ProductCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{
    public function index(ProductCategoryRepository $categoryRepository)
    {
        $categories = $categoryRepository->findAll();
        return $this->render('menu/index.html.twig', [
            'categories' => $categories,
        ]);
    }
}
