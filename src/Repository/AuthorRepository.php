<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Author|null find($id, $lockMode = null, $lockVersion = null)
 * @method Author|null findOneBy(array $criteria, array $orderBy = null)
 * @method Author[]    findAll()
 * @method Author[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Author::class);
    }

    // /**
    //  * @return Author[] Returns an array of Author objects
    //  */

    public function findBySurname($surname)
    {
//        return $this->_em->createQueryBuilder()
//            ->select('a.id, a.name, a.books')
//            ->from($this->_entityName, 'a', null)
//            ->getQuery()
//            ->getResult();

//        return $this->_em->createQueryBuilder()
//            ->select('a.id, a.name, a.surname, a.books')
//            ->from($this->_entityName, 'a', null)
//            ->andWhere('a.surname LIKE :surname')
//            ->setParameter('surname', $surname . '%')
//            ->getQuery()
//            ->getResult();
        return $this->createQueryBuilder('a')
            ->andWhere('a.surname LIKE :surname')
            ->setParameter('surname', $surname . '%')
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?Author
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
