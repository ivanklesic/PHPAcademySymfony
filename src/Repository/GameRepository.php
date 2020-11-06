<?php

namespace App\Repository;

use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
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

    public function getGamesOfGenreQueryBuilder($genre)
    {
        $builder = $this->getActiveQueryBuilder();
        $builder
            ->andWhere(':genre MEMBER OF g.genres')
            ->setParameter('genre', $genre);
        return $builder;
    }

    public function getGamesOfGenre($genre)
    {
        $builder = $this->getGamesOfGenreQueryBuilder($genre);
        return $builder->getQuery()->getResult();
    }

    public function findAllMatching($query, $genre = null)
    {
        if($genre)
        {
            $builder = $this->getGamesOfGenreQueryBuilder($genre);
        }
        else{
            $builder = $this->getActiveQueryBuilder();
        }
        return $builder
            ->andWhere('g.name LIKE :query')
            ->setParameter('query', '%'.$query.'%')
            ->getQuery()
            ->getResult()
            ;
    }


}
