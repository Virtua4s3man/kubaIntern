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
use App\Utils\ExportImport\AbastractImportEntityHelper;
use Doctrine\ORM\EntityManagerInterface;

class ProductImportHelper extends AbastractImportEntityHelper
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductCategoryRepository
     */
    private $categoryRepository;

    public function __construct(
        EntityManagerInterface $em,
        ProductRepository $productRepository,
        ProductCategoryRepository $categoryRepository
    ) {
        parent::__construct($em);
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function importData()
    {
        foreach ($this->generateNamedData() as $productData) {
            $product = $this->createProduct($productData);
            if ($product) {
                $this->em->persist($product);
            }
        }
        $this->em->flush();
    }

    private function createProduct($productData): ?Product
    {
        $product = $this->makeOrFetchProduct($productData['id']);
        unset($productData['id']);

        foreach ($productData as $key => $value) {
            if ('category' === $key) {
                $value = $this->makeOrFetchProductCategory($value);
            } elseif ('modificationDate' === $key || 'creationDate' === $key) {
                $value = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
            }
            $setter = $this->makeSetter($key);
            $product->$setter($value);
        }

        return $product;
    }

    private function makeOrFetchProduct(int $id): ?Product
    {
        $product = $this->productRepository->find($id);

        return $product ? $product : new Product();
    }

    private function makeOrFetchProductCategory(string $name): ?ProductCategory
    {
        if (!empty($name)) {
            $productCategory = $this->categoryRepository->findOneBy(['name' => $name]);
            if (null === $productCategory) {
                $productCategory = new ProductCategory();
                $productCategory->setName($name);
                $this->em->persist($productCategory);
            }

            return $productCategory;
        }

        return null;
    }
}
