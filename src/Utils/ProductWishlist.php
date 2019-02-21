<?php
/**
 * Created by PhpStorm.
 * User: virtua
 * Date: 2019-02-21
 * Time: 10:14
 */

namespace App\Utils;


use App\Entity\Product;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ProductWishlist
{
    private $wishlistPrefix = 'wishlist_product_';
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }


    public function add(Product $product)
    {
        if (null === $this->session) {
            return;
        }

        $id = $product->getId();
        $this->session->set($this->makeKey($id), $id);
    }

    public function getRefererUrl(Request $request): string
    {
        return $request->headers->get('referer');
    }

    /**
     * Prefix id with $this->wishlistPrefix
     */
    private function makeKey(int $id): string
    {
        return $this->wishlistPrefix . $id;
    }
}