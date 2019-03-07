<?php
/**
 * Created by PhpStorm.
 * User: virtua
 * Date: 2019-03-05
 * Time: 13:57
 */

namespace App\Utils\ExportImport\ImportHelpers;

use App\Entity\ProductCategory;
use App\Repository\ProductCategoryRepository;
use App\Utils\ExportImport\AbastractImportEntityHelper;
use Doctrine\ORM\EntityManagerInterface;

class CategoryImportHelper extends AbastractImportEntityHelper
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var ProductCategoryRepository
     */
    private $categoryRepository;

    public function __construct(
        EntityManagerInterface $em,
        ProductCategoryRepository $categoryRepository
    ) {
        parent::__construct($em);
        $this->categoryRepository = $categoryRepository;
    }

    public function importData()
    {
        foreach ($this->generateNamedData() as $categoryData) {
            $category = $this->createCategory($categoryData);
            if ($category) {
                $this->em->persist($category);
            }
        }
        $this->em->flush();
    }

    private function createCategory($categoryData): ?ProductCategory
    {
        $category = $this->makeOrFetchCategory($categoryData['id']);
        unset($categoryData['id']);

        foreach ($categoryData as $key => $value) {
            if ('modificationDate' === $key || 'creationDate' === $key) {
                $value = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
            }
            $setter = $this->makeSetter($key);
            $category->$setter($value);
        }

        return $category;
    }

    private function makeOrFetchCategory(int $id): ?ProductCategory
    {
        $category = $this->categoryRepository->find($id);

        return $category ? $category : new ProductCategory();
    }
}
