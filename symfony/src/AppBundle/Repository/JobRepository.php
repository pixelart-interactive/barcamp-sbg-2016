<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Category;
use AppBundle\Entity\Job;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class JobRepository extends EntityRepository
{
    /**
     * @param Category $category The category to search the active jobs
     * @param int      $limit    The limit to fetch
     * @param int      $offset   The offset for pagination
     *
     * @return Job[]|Paginator
     */
    public function findActiveJobs(Category $category, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('job')
            ->where('job.category = :category')
            ->andWhere('job.expiresAt > CURRENT_TIMESTAMP()')
            ->setParameter('category', $category)
            ->orderBy('job.expiresAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
        ;

        return new Paginator($qb, false);
    }
}
