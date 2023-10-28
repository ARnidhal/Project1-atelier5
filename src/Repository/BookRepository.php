<?php

namespace App\Repository;

use App\Entity\Book;
use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }
    public function showbyiidauthor($ref){
        return $this->createQueryBuilder('b')
      
        ->where('b.ref=:ref')
        ->setParameter('ref',$ref)
        ->getQuery()
        ->getResult();


    }
    public function orderbyauthor(){ //tri
        return $this->createQueryBuilder('b')
        ->orderBy('b->author.username','ASC')
        ->getQuery()
        ->getResult();
    }
    public function listBooksBefore2023() 
    {
        return $this->createQueryBuilder('book')
        ->join('book.authors', 'author')
        ->Where('author.nbr_books > 35')
        ->andWhere('book.publicationDate < :date')
        ->groupBy('author.nbr_books')
        ->setParameter('date', new \DateTime('2023-01-01'))
        ->getQuery()
        ->getResult();
    }
    public function updateCategory()
    {
        return $this->createQueryBuilder('book')
            ->innerJoin('book.authors', 'author')
            ->where('author.username = :authorName')
            ->setParameter('authorName', 'William Shakesspear')
            ->getQuery()
            ->getResult();
    }
   
    function NbBookCategory(){
        $em=$this->getEntityManager();
        return $em
        ->createQuery('SELECT count(b) from App\Entity\Book b WHERE b.category=:category')
        ->setParameter('category','Science-Fiction')
        ->getSingleScalarResult();
    }
    function findBookByPublicationDate(){
        $em=$this->getEntityManager();
        return $em->createQuery('SELECT b from App\Entity\Book b WHERE 
        b.publicationDate BETWEEN ?1 AND ?2')
        ->setParameter(1,'2014-01-01')
        ->setParameter(2,'2018-12-31')->getResult();
    }
    public function ShowBookOrderByAuthor()
    {
        return $this->createQueryBuilder('a')
        ->orderBy('a.authors', 'ASC')
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return Book[] Returns an array of Book objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Book
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
