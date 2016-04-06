<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Category;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;

class CategoryRepository extends EntityRepository
{
    /**
     * @return Category[]|Collection
     */
    public function findAllWithActiveJobs()
    {
        $qb = $this->createQueryBuilder('category')
            ->innerJoin('category.jobs', 'job')
            ->where('job.expiresAt > CURRENT_TIMESTAMP()')
        ;

        return $qb->getQuery()->getResult();
    }
}
