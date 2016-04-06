<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
}
