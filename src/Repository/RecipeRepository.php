<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recipe>
 *
 * @method Recipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recipe[]    findAll()
 * @method Recipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }
    /**
     * this methode use QueryBuilder and allow to find public recipes and its number
     *
     * @param integer $nbRecipes
     * @return array
     */
    public function findPublicRecipe(int $nbRecipes): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.isPublic = 1')
            ->orderBy('r.createdAt', 'DESC')
            ->setMaxResults($nbRecipes)
            ->getQuery()
            ->getResult();
    }
}
