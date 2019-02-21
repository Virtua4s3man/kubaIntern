<?php
/**
 * Created by PhpStorm.
 * User: virtua
 * Date: 2019-02-21
 * Time: 14:42
 */

namespace App\Utils;

use App\Entity\Product;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ProductLogger
{
    const DISPLAYED = 'displayed';
    const UPDATED = 'updated';
    const DELETED = 'deleted';

    private $logger;
    private $requestStack;

    public function __construct(LoggerInterface $logger, RequestStack $requestStack)
    {
        $this->logger = $logger;
        $this->requestStack = $requestStack;
    }

    public function logDisplayed(Product $product)
    {
        $this->logAction($product, self::DISPLAYED);
    }

    public function logUpdated(Product $product)
    {
        $this->logAction($product, self::UPDATED);
    }

    public function logDeleted(Product $product)
    {
        $this->logAction($product, self::DELETED);
    }

    private function logAction(Product $product, $action)
    {
        $ip = $this->requestStack->getCurrentRequest()->getClientIp();
        $id = $product->getId();
        $this->logger->info('Product with id: ' . $id . ' ' . $action . ' by ' . $ip);
    }
}
