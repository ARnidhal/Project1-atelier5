<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Author>
 *
 * @method Author|null find($id, $lockMode = null, $lockVersion = null)
 * @method Author|null findOneBy(array $criteria, array $orderBy = null)
 * @method Author[]    findAll()
 * @method Author[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    public function orderbyusername(){ //tri
        return $this->createQueryBuilder('a')
        ->orderBy('a.username','ASC')
        ->getQuery()
        ->getResult();
    }


public function seachwithalph(){ //recherche
        return $this->createQueryBuilder('a')
        ->where('a.username Like :username')
        ->setParameter('username','w%')
        ->getQuery()
        ->getResult();
    }

    public function showbyidauthor($id){
        return $this->createQueryBuilder('a')
        ->join('a.books','b')
        ->addSelect('b')
        ->where('b.authors=:id')
        ->setParameter('id',$id)
        ->getQuery()
        ->getResult();

    }
    function SearchAuthorminmax($min,$max){
        $em=$this->getEntityManager();
        return $em
        ->createQuery(
            'SELECT a from App\Entity\Author a WHERE 
            a.nbr_books BETWEEN ?1 AND ?2')
            ->setParameter(1,$min)
            ->setParameter(2,$max)
            ->getResult();
    }
    function DeleteAuthorwith0books(){
        $em=$this->getEntityManager();
        return $em
        ->createQuery(
            'DELETE App\Entity\Author a WHERE a.nbr_books = 0')
        ->getResult();
    }
   
//    /**
//     * @return Author[] Returns an array of Author objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Author
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
