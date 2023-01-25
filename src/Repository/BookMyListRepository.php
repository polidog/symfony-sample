<?php

namespace App\Repository;

use App\Entity\BookMyList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<BookMyList>
 *
 * @method BookMyList|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookMyList|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookMyList[]    findAll()
 * @method BookMyList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookMyListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookMyList::class);
    }

    public function save(BookMyList $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(BookMyList $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getPage($currentPage = 1, $limit = 10)
    {
        $query = $this->createQueryBuilder('b')
            ->orderBy('b.id', 'ASC')
            ->getQuery();

        return $this->paginate($query, $currentPage, $limit);
    }

    public function paginate($dql, $page = 1, $limit = 10)
    {
        $paginator = new Paginator($dql);

        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);

        return $paginator;
    }

//    /**
//     * @return BookMyList[] Returns an array of BookMyList objects
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

//    public function findOneBySomeField($value): ?BookMyList
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
