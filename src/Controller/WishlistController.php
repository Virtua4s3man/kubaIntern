<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Attribute\NamespacedAttributeBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/wishlist", methods={})
 */
class WishlistController extends AbstractController
{
    private $sessionNamespace = 'wishlist/';
    private $redirectionRoute = 'product_index';

    /**
     * @Route("/{id}", name="wishlist_add", methods={"POST"})
     */
    public function wishListAdd(Product $product)
    {
        $id = $product->getId();
        $session = new Session(new NativeSessionStorage(), new NamespacedAttributeBag());
        $session->set($this->namespaceName($id), $id);

        $this->redirectToRoute($this->redirectionRoute);
    }

    /**
     * @Route("/{id}", name="wishlist_remove", methods={"DELETE"})
     */
    public function wishListRemove(Product $product)
    {
        $id = $product->getId();
        $session = new Session(new NativeSessionStorage(), new NamespacedAttributeBag());
        $session->remove($this->namespaceName($id));

        $this->redirectToRoute($this->redirectionRoute);
    }

    /**
     * @Route("/clear", name="wishlist_clear", methods={"DELETE"})
     */
    public function wishListClear()
    {

    }

    private function namespaceName($name): string
    {
        return $this->sessionNamespace . $name;
    }
}
