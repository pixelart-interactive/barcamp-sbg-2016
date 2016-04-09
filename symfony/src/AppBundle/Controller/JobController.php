<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Job;
use AppBundle\Form\JobType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
     * @Method({"GET", "POST"})
     * @Route("/job/new", name="job_new")
     */
    public function newAction(Request $request)
    {
        $job = new Job();
        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($job);
            $em->flush();

            return $this->redirectToRoute('job_show', [
                'id' => $job->getId(),
            ]);
        }

        return $this->render('job/new.html.twig', [
            'job' => $job,
            'form' => $form->createView(),
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
