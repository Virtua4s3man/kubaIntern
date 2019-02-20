<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Attribute\NamespacedAttributeBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/wishlist", methods={})
 */
class WishlistController extends AbstractController
{
    private $sessionNamespace = 'wishlist';
    private $redirectionRoute = 'product_index';

    /**
     * @Route("/{id}", name="wishlist_add", methods={"POST"})
     */
    public function wishListAdd(Request $request, Product $product)
    {
        if ($this->isCsrfTokenValid('add'.$product->getId(), $request->request->get('_token'))) {
            $this->addToWishlist($product);
        }

        return $this->redirectToRoute($this->redirectionRoute);
    }

    /**
     * @Route("/{id}", name="wishlist_remove", methods={"DELETE"})
     */
    public function wishListRemove(Request $request, Product $product)
    {
        $id = $product->getId();
        if ($this->isCsrfTokenValid('add'.$product->getId(), $request->request->get('_token'))) {
            $session = new Session(new NativeSessionStorage(), new NamespacedAttributeBag());
            $session->remove($this->namespaceName($id));
        }

        $this->redirectToRoute($this->redirectionRoute);
    }

    /**
     * @Route("/clear", name="wishlist_clear", methods={"DELETE"})
     */
    public function wishListClear()
    {

    }

    private function addToWishlist(Product $product)
    {
        $id = $product->getId();
        $session = new Session(new NativeSessionStorage(), new NamespacedAttributeBag());
        if (5 > $this->countWishlistProducts($session)) {
            $session->set($this->namespaceName($id), $id);
            $this->addFlash('success', $product->getName() . ' added to wishlist');
        } else {
            $this->addFlash('warning', 'wishlist can\'t contain more than 5 products');
        }
    }

    private function countWishlistProducts(Session $session)
    {
        return count($this->getWishlist($session));
    }

    private function getWishlist(Session $session): array
    {
        $wishlist = $session->get($this->sessionNamespace);
        return $wishlist ? $wishlist : [];
    }

    private function namespaceName($name): string
    {
        return $this->sessionNamespace . '/'. $name;
    }
}
