<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Picture;
use App\Entity\Pro;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pro>
 *
 * @method Pro|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pro|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pro[]    findAll()
 * @method Pro[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pro::class);
    }

    /**
     * Undocumented function
     *
     * @param integer $department
     * @param Category $category
     * @param integer $offset
     * @param integer $limit
     * @return Pro[]
     */
    public function findByDepartmentAndCategoryHydratedWithFirstPicture(int $department, Category $category, int $offset = 0, int $limit = 4)
    {
        /** @var CategoryRepository */
        $categoryRepository = $this->getEntityManager()->getRepository(Category::class);

        /** @var PictureRepository */
        $pictureRepository = $this->getEntityManager()->getRepository(Picture::class);

        

        $pros = $this->createQueryBuilder('p')
                ->select('p')
                ->andWhere('p.id IN(:ids)')
                ->setParameter('ids', $categoryRepository->findProIdsForOneCategory($category))
                ->groupBy('p')
                ->andWhere('p.departments LIKE :department')
                ->setParameter('department', '%'.$department.'%')
                ->orderBy('p.id', 'ASC')
                ->setMaxResults($limit)
                ->setFirstResult($offset)
                ->getQuery()
                ->getResult()
                ;

        $prosById = [];
        foreach($pros as $pro)
        {
            $prosById[$pro->getId()] = $pro;
        }
        $this->hydrateProsWithCategories($prosById);
        $pictureRepository->hydrateProsWithFirstPicture($pros);
        return $pros;
    }

    /**
     * Undocumented function
     *
     * @param array $prosByIds
     * @return void
     */
    public function hydrateProsWithCategories($prosById)
    {
        $pro_category = $this->createQueryBuilder('p')
                            ->select('p.id as pro_id', 'c.name as category_name')
                            ->join('p.categories', 'c')
                            ->andWhere('p.id IN(:ids)')
                            ->setParameter('ids', array_keys($prosById))
                            ->getQuery()
                            ->getResult()
                            ;
        

        foreach($pro_category as $pc) 
        {
            $prosById[$pc['pro_id']]->addCategoryName($pc['category_name']);
        }
    }

    public function add(Pro $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Pro $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Pro[] Returns an array of Pro objects
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

//    public function findOneBySomeField($value): ?Pro
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
