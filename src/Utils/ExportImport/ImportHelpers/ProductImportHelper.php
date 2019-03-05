<?php
/**
 * Created by PhpStorm.
 * User: virtua
 * Date: 2019-03-05
 * Time: 13:57
 */

namespace App\Utils\ExportImport\ImportHelpers;

use App\Entity\Product;
use App\Entity\ProductCategory;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductRepository;
use App\Utils\ExportImport\ImportProductHelper;
use Doctrine\ORM\EntityManagerInterface;

class ProductImportHelper extends ImportProductHelper
{
    public function importData(EntityManagerInterface $em, ProductRepository $productRepository, ProductCategoryRepository $categoryRepository)
    {
        foreach ($this->generateNamedData() as $productData) {
            $product = $this->makeEntity(
                $productData,
                $productRepository,
                $categoryRepository
            );
            if ($product) {
                $em->persist($product);
            }
            dump($product);
        }
        $em->flush();
    }

    private function makeEntity(
        $productData,
        ProductRepository $productRepository,
        ProductCategoryRepository $categoryRepository
    ): ?Product {
        $product = $productRepository->find($productData['id']);
        unset($productData['id']);
        $product = $product ? : new Product();

        foreach ($productData as $key => $value) {
            if ('category' === $key and !empty($value)) {
                $productCategory = $categoryRepository->findOneBy(['name' => $value]);
                if (null === $productCategory) {
                    $productCategory = new ProductCategory();
                    $productCategory->setName($value);
                }
                $product->setCategory($productCategory);
            } elseif ('modificationDate' === $key || 'creationDate' === $key) {
                $setter = $this->makeSetter($key);
                $product->$setter(\DateTime::createFromFormat('Y-m-d H:i:s', $value));
            } else {
                $setter = $this->makeSetter($key);
                $product->$setter($value);
            }
        }

        return $product;
    }
}