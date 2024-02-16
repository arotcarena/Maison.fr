<?php

namespace App\Repository;

use App\Entity\Picture;
use App\Entity\Pro;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Picture>
 *
 * @method Picture|null find($id, $lockMode = null, $lockVersion = null)
 * @method Picture|null findOneBy(array $criteria, array $orderBy = null)
 * @method Picture[]    findAll()
 * @method Picture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PictureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Picture::class);
    }

    /** 
     * @param Pro[] $pros
     */
    public function hydrateProsWithFirstPicture(array $pros): void 
    {
        $prosById = [];
        foreach($pros as $pro)
        {
            $prosById[$pro->getId()] = $pro;
        }

        // /** @var Picture[] */
        // $pictures = $this->createQueryBuilder('p')
        //         ->select('p')
        //         ->where('p.id IN (:ids)')
        //         ->setParameter(
        //             'ids', 
        //             $this->createQueryBuilder('pic')
        //                     ->select('MAX(pic.id)')
        //                     ->where('pic.pro IN(:pros)')
        //                     ->groupBy('pic.pro')
        //                     ->setParameter('pros', $pros)
        //                     ->getQuery()
        //                     ->getResult()
        //         )
        //         ->getQuery()
        //         ->getResult()
        //         ;

        $qb = $this->createQueryBuilder('p');
        $pictures = $qb
                        ->select('p')
                        ->where(
                            $qb->expr()->in(
                                'p.id', 
                                $this->createQueryBuilder('pic')
                                        ->select('MAX(pic.id)')
                                        ->where('pic.pro IN(:pros)')
                                        ->groupBy('pic.pro')
                                        ->getDQL()
                            )
                        )
                        ->setParameter('pros', $pros)
                        ->getQuery()
                        ->getResult()
                        ;
        
        foreach($pictures as $picture)
        {
            /** @var Pro */
            $pro = $prosById[$picture->getPro()->getId()];
            $pro->setFirstPicture($picture);
        }
    }

    public function add(Picture $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Picture $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Picture[] Returns an array of Picture objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Picture
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
