<?php

namespace App\Repository;

use App\Entity\Book;
use App\Utils\RandomBookService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    /**
     * @var RandomBookService
     */
    private $randomBookService;

    public function __construct(RegistryInterface $registry, RandomBookService $randomBookService)
    {
        parent::__construct($registry, Book::class);
        $this->randomBookService = $randomBookService;
    }

    /**
     * Gets random book
     * @return Book
     */
    public function getRandomBook(): ?Book
    {
        $bookAmount = $this->count([]);
        $book = $bookAmount ? $this->findBy([], null, 1, rand()%$bookAmount)[0] : null;
        if ($book instanceof Book) {
            $id = $book->getId();
            $this->randomBookService->log($id);
            $this->randomBookService->save($id);
        }
        return $book;
    }

    // /**
    //  * @return Book[] Returns an array of Book objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Book
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
