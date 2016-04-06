<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Category;
use AppBundle\Entity\Job;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;

class JobRepository extends EntityRepository
{
    /**
     * @param Category $category The category to search the active jobs
     * @param int      $limit    The limit to fetch
     *
     * @return Job[]|Collection
     */
    public function findActiveJobs(Category $category, $limit = null)
    {
        $qb = $this->createQueryBuilder('job')
            ->where('job.category = :category')
            ->andWhere('job.expiresAt > CURRENT_TIMESTAMP()')
            ->setParameter('category', $category)
            ->orderBy('job.expiresAt', 'DESC')
            ->setMaxResults($limit)
        ;

        return $qb->getQuery()->getResult();
    }
}
