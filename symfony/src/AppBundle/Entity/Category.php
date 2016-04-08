<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryRepository")
 */
class Category
{
    const MAX_JOBS_HOMEPAGE = 10;
    const MAX_JOBS_PAGE = 20;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="Job", mappedBy="category", fetch="EXTRA_LAZY")
     *
     * @var Job[]|Collection
     */
    private $jobs;

    /**
     * @var Job[]|Collection
     */
    private $activeJobs;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->jobs = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     *
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param Job[]|Collection $jobs
     */
    public function setJobs($jobs)
    {
        $this->jobs = $jobs;
    }

    /**
     * @param Job $job
     *
     * @return Category
     */
    public function addJob(Job $job)
    {
        $this->jobs[] = $job;

        return $this;
    }

    /**
     * @param Job $job
     */
    public function removeJob(Job $job)
    {
        $this->jobs->removeElement($job);
    }

    /**
     * @return Job[]|Collection
     */
    public function getJobs()
    {
        return $this->jobs;
    }

    /**
     * @return Job[]|Collection
     */
    public function getActiveJobs()
    {
        return $this->activeJobs;
    }

    /**
     * @param Job[]|Collection $activeJobs
     */
    public function setActiveJobs($activeJobs)
    {
        $this->activeJobs = $activeJobs;
    }
}
