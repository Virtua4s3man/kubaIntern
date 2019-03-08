<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Utils\ProductLogger;
use App\Utils\ProductWishlist;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/product")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="product_index", methods={"GET"})
     */
    public function index(ProductRepository $productRepository, ProductWishlist $wishlist): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
            'ids_on_wishlist' => $wishlist->getIdsOnWishlist(),
        ]);
    }

    /**
     * @Route("/new", name="product_new", methods={"GET","POST"})
     */
    public function new(Request $request, TranslatorInterface $translator): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash(
                'success',
                $translator->trans("product has been added")
            );

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_show", methods={"GET"})
     */
    public function show(Product $product, ProductWishlist $wishlist, ProductLogger $logger): Response
    {
        $logger->logDisplayed($product);

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'ids_on_wishlist' => $wishlist->getIdsOnWishlist(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product_edit", methods={"GET","POST"})
     */
    public function edit(
        Request $request,
        Product $product,
        ProductLogger $logger,
        TranslatorInterface $translator
    ): Response {
        $form = $this->createForm(ProductType::class, $product);
        $gallery = $product->getGallery()->toArray();
        $cover = $product->getCover();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->productUpdate($cover, $gallery, $product);
            $this->addFlash(
                'success',
                $translator->trans("product has been updated")
            );

            $logger->logUpdated($product);

            return $this->redirectToRoute('product_index', [
                'id' => $product->getId(),
            ]);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_delete", methods={"DELETE"})
     */
    public function delete(
        Request $request,
        Product $product,
        ProductLogger $logger,
        TranslatorInterface $translator
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $logger->logDeleted($product);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();

            $this->addFlash(
                'success',
                $translator->trans("product has been deleted")
            );
        }

        return $this->redirectToRoute('product_index');
    }

    /**
     * @Route("/image/{id}", name="product_image_delete", methods={"DELETE"})
     */
    public function galleryImageDelete(Image $image, Request $request, TranslatorInterface $translator): Response
    {
        if ($this->isCsrfTokenValid('delete'.$image->getId(), $request->request->get('_token'))) {
            $this->addFlash(
                'success',
                $translator->trans("gallery image has been deleted")
            );

            $em = $this->getDoctrine()->getManager();
            $em->remove($image);
            $em->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/wishlist/{id<\d+>}", name="wishlist_add", methods={"POST"})
     */
    public function wishlistAdd(Request $request, Product $product, ProductWishlist $wishlist): Response
    {
        if ($this->isCsrfTokenValid('add'.$product->getId(), $request->request->get('_token'))) {
            $wishlist->add($product);
        }

        return $this->redirect($wishlist->getRefererUrl($request));
    }

    /**
     * @Route("/wishlist/{id<\d+>}", name="wishlist_remove", methods={"DELETE"})
     */
    public function wishlistRemove(Request $request, Product $product, ProductWishlist $wishlist): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $wishlist->remove($product);
        }

        return $this->redirect($wishlist->getRefererUrl($request));
    }

    /**
     * @Route("/wishlist/clear/", name="wishlist_clear", methods={"DELETE"})
     */
    public function wishlistClear(Request $request, ProductWishlist $wishlist): Response
    {
        if ($this->isCsrfTokenValid('clear', $request->request->get('_token'))) {
            $wishlist->clear();
        }

        return $this->redirect($wishlist->getRefererUrl($request));
    }

    private function productUpdate(Image $cover, array $gallery, Product &$product)
    {
        if (null === $product->getCover()) {
            $product->setCover($cover);
        }
        $newGalery = array_merge($gallery, $product->getGallery()->toArray());
        foreach ($newGalery as $image) {
            $product->addGallery($image);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();
    }
}
