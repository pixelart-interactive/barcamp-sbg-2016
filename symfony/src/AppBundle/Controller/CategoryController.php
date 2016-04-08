<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("category")
 */
class CategoryController extends Controller
{
    /**
     * @Method("GET")
     * @Route("/{id}", name="category_show")
     */
    public function showAction(Category $category, Request $request)
    {
        $page = (int) $request->query->get('p', 1);
        $em = $this->getDoctrine()->getManager();

        $jobs = $em->getRepository('AppBundle:Job')
            ->findActiveJobs(
                $category,
                Category::MAX_JOBS_PAGE,
                Category::MAX_JOBS_PAGE * ($page - 1)
            )
        ;

        $category->setActiveJobs($jobs);

        $totalItems = count($jobs);
        $pagesCount = ceil($totalItems / Category::MAX_JOBS_PAGE);

        return $this->render('category/show.html.twig', [
            'category' => $category,
            'total_jobs' => $totalItems,
            'pages_count' => $pagesCount,
            'current_page' => $page,
        ]);
    }
}
