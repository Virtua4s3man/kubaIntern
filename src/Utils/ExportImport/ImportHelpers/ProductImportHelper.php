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
use App\Utils\ExportImport\ImportProductHelper;
use Doctrine\ORM\EntityManagerInterface;

class ProductImportHelper extends ImportProductHelper
{

    public function importData(EntityManagerInterface $em, ProductCategoryRepository $categoryRepository)
    {
        foreach ($this->generateNamedData() as $productData) {
            $product = $this->makeEntity($productData, $categoryRepository);
            $em->persist($product);
        }
        $em->flush();
    }

    private function makeEntity($productData, ProductCategoryRepository $categoryRepository): Product
    {
        $product = new Product();
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