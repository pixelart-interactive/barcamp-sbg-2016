<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Job;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class JobController extends Controller
{
    /**
     * @Method("GET")
     * @Route("/", name="job_index")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $categories = $em->getRepository('AppBundle:Category')->findAllWithActiveJobs();
        foreach ($categories as $category) {
            $jobs = $em->getRepository('AppBundle:Job')->findActiveJobs($category, Category::MAX_JOBS_HOMEPAGE);
            $category->setActiveJobs($jobs);
        }

        return $this->render('job/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Method("GET")
     * @Route("/job/{id}", name="job_show")
     * @ParamConverter("job", options={
     *     "repository_method" = "findActiveJob",
     *     "map_method_signature" = true
     * })
     */
    public function showAction(Job $job)
    {
        return $this->render('job/show.html.twig', [
            'job' => $job,
        ]);
    }
}
