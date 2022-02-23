<?php

namespace App\Repository;

use App\Entity\Messages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Pagination\Paginator;
use function Symfony\Component\String\u;

/**
 * @method Messages|null find($id, $lockMode = null, $lockVersion = null)
 * @method Messages|null findOneBy(array $criteria, array $orderBy = null)
 * @method Messages[]    findAll()
 * @method Messages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Messages::class);
    }

    // /**
    //  * @return Messages[] Returns an array of Messages objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Messages
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    // /*
    // * 
    // */
    // public function findLatest(int $page = 1, Tag $tag = null): Paginator
    // {
    //     $qb = $this->createQueryBuilder('p')
    //         ->addSelect('a', 't')
    //         ->innerJoin('p.author', 'a')
    //         ->leftJoin('p.tags', 't')
    //         ->where('p.publishedAt <= :now')
    //         ->orderBy('p.publishedAt', 'DESC')
    //         ->setParameter('now', new \DateTime());

    //     if (null !== $tag) {
    //         $qb->andWhere(':tag MEMBER OF p.tags')
    //             ->setParameter('tag', $tag);
    //     }

    //     return (new Paginator($qb))->paginate($page);
    // }


    /**
     * @return Messages[]
     */
    public function findBySearchQuery(string $query, int $limit = Paginator::PAGE_SIZE): array
    {
        $searchTerms = $this->extractSearchTerms($query);

        if (0 === \count($searchTerms)) {
            return [];
        }

        $queryBuilder = $this->createQueryBuilder('p');

        foreach ($searchTerms as $key => $term) {
            $queryBuilder
                ->orWhere('p.Text LIKE :t_' . $key)
                ->setParameter('t_' . $key, '%' . $term . '%');
        }

        return $queryBuilder
            ->orderBy('p.Timestamp', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Transforms the search string into an array of search terms.
     */
    private function extractSearchTerms(string $searchQuery): array
    {
        $searchQuery = u($searchQuery)->replaceMatches('/[[:space:]]+/', ' ')->trim();
        $terms = array_unique($searchQuery->split(' '));

        // ignore the search terms that are too short
        return array_filter($terms, function ($term) {
            return 2 <= $term->length();
        });
    }
}
