<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product")
 */
class ProductController extends AbstractController
{
    private $wishlistPrefix = 'wishlist_product_';

    /**
     * @Route("/", name="product_index", methods={"GET"})
     */
    public function index(Request $request, ProductRepository $productRepository): Response
    {
        if ($request->hasSession()) {
            $session = $request->getSession();
        }

        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
            'ids_on_wishlist' => array_values($this->getWishlist($session)),
        ]);
    }

    /**
     * @Route("/new", name="product_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
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
                "Product has been added"
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
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'success',
                "Product has been updated"
            );


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
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();

            $this->addFlash(
                'success',
                "Product has been deleted"
            );
        }

        return $this->redirectToRoute('product_index');
    }

    /**
     * @Route("/wishlist/{id}", name="wishlist_add", methods={"POST"})
     */
    public function wishlistAdd(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('add'.$product->getId(), $request->request->get('_token'))) {
            if ($request->hasSession()) {
                $session = $request->getSession();
                $this->addToWishlist($session, $product);
            }
        }

        return $this->redirectToRoute('product_index');
    }

    /**
     * @Route("/wishlist/clear", name="wishlist_clear", methods={"DELETE"})
     */
    public function wishlistClear(Request $request): Response
    {
        if ($this->isCsrfTokenValid('clear', $request->request->get('_token'))) {
            if ($request->hasSession()) {
                $session = $request->getSession();
                $this->clearWishlist($session);
            }
        }

        return $this->redirectToRoute('product_index');
    }

    /**
     * @Route("/wishlist/{id}", name="wishlist_remove", methods={"DELETE"})
     */
    public function wishlistRemove(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            if ($request->hasSession()) {
                $session = $request->getSession();
                $session->remove(
                    $this->makeWishlistName($product->getId())
                );
            }
        }
        return $this->redirectToRoute('product_index');
    }

    /**
     * Clears session variables prefixed by $wishlistprefix
     */
    private function clearWishlist(Session $session)
    {
        $keys = array_keys($this->getWishlist($session));
        foreach ($keys as $key) {
            $session->remove($key);
        }
    }

    /**
     * Adds product to wishlist
     */
    private function addToWishlist(SessionInterface $session, Product $product)
    {
        $id = $product->getId();
        if (5 > count($this->getWishlist($session))) {
            $session->set($this->makeWishlistName($id), $id);
            $this->addFlash('success', $product->getName() . ' added to wishlist');
        } else {
            $this->addFlash('warning', 'wishlist can\'t contain more than 5 products');
        }
    }

    /**
     * Prefix id with $this->wishlistPrefix
     */
    private function makeWishlistName(int $id): string
    {
        return $this->wishlistPrefix . $id;
    }

    /**
     * Returns session varaiables prefixed with $this->wishlistprefix
     * @param Session $session
     * @return array
     */
    private function getWishlist(Session $session): array
    {
        return array_filter(
            $session->all(),
            function ($key) {
                return strpos($key, $this->wishlistPrefix) === 0;
            },
            ARRAY_FILTER_USE_KEY
        );
    }
}
