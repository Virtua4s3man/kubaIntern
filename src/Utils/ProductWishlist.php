<?php
/**
 * Created by PhpStorm.
 * User: virtua
 * Date: 2019-02-21
 * Time: 10:14
 */

namespace App\Utils;


use App\Entity\Product;
use Symfony\Component\HttpFoundation\Session\Session;

class ProductWishlist
{
    private $wishlistPrefix = 'wishlist_product_';

    public function add(Session $session, Product $product)
    {
        $id = $product->getId();
        $session->set($this->makeKey($id), $id);
    }

    /**
     * Prefix id with $this->wishlistPrefix
     */
    private function makeKey(int $id): string
    {
        return $this->wishlistPrefix . $id;
    }
}