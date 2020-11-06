<?php

namespace App\Repository;

use App\Entity\Genre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Genre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Genre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Genre[]    findAll()
 * @method Genre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GenreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Genre::class);
    }

    public function getActiveQueryBuilder()
    {
        return $this->createQueryBuilder('g')
            ->where('g.deleted = :deleted')
            ->setParameter('deleted', 0)
            ;
    }

    public function getActive()
    {
        $builder = $this->getActiveQueryBuilder();
        return $builder->getQuery()->getResult();
    }
}
