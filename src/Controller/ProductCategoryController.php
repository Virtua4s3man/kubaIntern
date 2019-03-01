<?php

namespace App\Controller;

use App\Entity\ProductCategory;
use App\Form\ProductCategoryType;
use App\Repository\ProductCategoryRepository;
use App\Utils\ProductWishlist;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/product/category")
 */
class ProductCategoryController extends AbstractController
{
    /**
     * @Route("/", name="product_category_index", methods={"GET"})
     */
    public function index(ProductCategoryRepository $productCategoryRepository): Response
    {
        dump(
            \DateTime::createFromFormat('Y-m-d H:i:s', "2019-02-28 14:44:16")
        );

        return $this->render('product_category/index.html.twig', [
            'product_categories' => $productCategoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="product_category_new", methods={"GET","POST"})
     */
    public function new(Request $request, TranslatorInterface $translator): Response
    {
        $productCategory = new ProductCategory();
        $form = $this->createForm(ProductCategoryType::class, $productCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($productCategory);
            $entityManager->flush();

            $this->addFlash(
                'success',
                $translator->trans("category has been added")
            );

            return $this->redirectToRoute('product_category_index');
        }

        return $this->render('product_category/new.html.twig', [
            'product_category' => $productCategory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_category_show", methods={"GET"})
     */
    public function show(ProductCategory $productCategory, ProductWishlist $wishlist): Response
    {
        return $this->render('product_category/show.html.twig', [
            'product_category' => $productCategory,
            'ids_on_wishlist' => $wishlist->getIdsOnWishlist(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product_category_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ProductCategory $productCategory, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(ProductCategoryType::class, $productCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'success',
                $translator->trans("category has been updated")
            );

            return $this->redirectToRoute('product_category_index', [
                'id' => $productCategory->getId(),
            ]);
        }

        return $this->render('product_category/edit.html.twig', [
            'product_category' => $productCategory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_category_delete", methods={"DELETE"})
     */
    public function delete(
        Request $request,
        ProductCategory $productCategory,
        TranslatorInterface $translator
    ): Response {
        if ($productCategory->hasProducts()) {
            $this->addFlash(
                'warning',
                $translator->trans("can not remove category containing products")
            );

            return $this->redirectToRoute('product_category_index');
        }

        if ($this->isCsrfTokenValid('delete'.$productCategory->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($productCategory);
            $entityManager->flush();

            $this->addFlash(
                'success',
                $translator->trans("category has been deleted")
            );
        }

        return $this->redirectToRoute('product_category_index');
    }
}
