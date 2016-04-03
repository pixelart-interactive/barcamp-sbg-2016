Step 05: More with the view
---------------------------

As the story F2 says, each category should have it's own page to list all jobs.
To do so, you need a new controller and correlating views.

### 1. The new category controller

Create a new category controller with the following content to get the requested
category and output the jobs list:

```php
<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("category")
 */
class CategoryController extends Controller
{
    /**
     * @Method("GET")
     * @Route("/{id}", name="category_show")
     */
    public function showAction(Category $category)
    {
        $em = $this->getDoctrine()->getManager();

        $jobs = $em->getRepository('AppBundle:Job')->findActiveJobs($category);
        $category->setActiveJobs($jobs);

        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }
}
```

### 2. Category jobs list template

As you can see in the controller above we need to create the `category/show.html.twig`
template:

```twig
{% extends 'base.html.twig' %}

{% block title %}
    Jobs in the {{ category.name }} category
{% endblock %}

{% block stylesheets %}
    {{ parent() }}
    <link rel="stylesheet" href="{{ asset('css/jobs.css') }}" type="text/css" media="all" />
{% endblock %}

{% block content %}
    <div class="category">
        <h1>{{ category.name }}</h1>
    </div>

    <table class="jobs">
        {% for job in category.activeJobs %}
            <tr class="{{ cycle(['even', 'odd'], loop.index) }}">
                <td class="location">{{ job.location }}</td>
                <td class="position">
                    <a href="#">
                        {{ job.position }}
                    </a>
                </td>
                <td class="company">{{ job.company }}</td>
            </tr>
        {% endfor %}
    </table>
{% endblock %}
```

And to link the category name in the jobs index to the category page you can add a
`<a>` around the category name:

```twig
<a href="{{ path('category_show', {id: category.id}) }}">{{ category.name }}</a>
```

If all is ok you can click on the category name

### 4. Refactor common template parts

If you have notices the html and twig code for the jobs table is the same So
lets refactor it.

Get the table `<table class="jobs">` out of both files and put it in a separate
`job/_jobs.html.twig` file. And then you can modify `category.jobs` to `jobs`:

```twig
<table class="jobs">
    {% for job in jobs %}
        <tr class="{{ cycle(['even', 'odd'], loop.index) }}">
            <td class="location">{{ job.location }}</td>
            <td class="position">
                <a href="#">
                    {{ job.position }}
                </a>
            </td>
            <td class="company">{{ job.company }}</td>
        </tr>
    {% endfor %}
</table>
```

Now you can include this partial in your `job/index.html` and `category/show.html.twig`
with:

```twig
{{ include('job/_jobs.html.twig', {jobs: category.activeJobs}) }}
```

Both pages should still look the same.

### 5. Pagination

To paginate the category view you have to extend the jobs query builder first:

```php
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
```

And then modify the controller to use a page parameter and calculate all the
stuff:

```php
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
```

This leads to the following pagination snippet to add after the jobs table:

```twig
    {% if pages_count > 1 %}
        <div class="pagination">
            <a href="{{ path('category_show', {id: category.id, p: 1 }) }}">
                <img src="{{ asset('images/first.png') }}" alt="First page" title="First page" />
            </a>

            <a href="{{ path('category_show', {id: category.id, p: current_page - 1 }) }}">
                <img src="{{ asset('images/previous.png') }}" alt="Previous page" title="Previous page" />
            </a>

            {% for page in 1..pages_count %}
                {% if page == current_page %}
                    {{ page }}
                {% else %}
                    <a href="{{ path('category_show', {id: category.id, p: page }) }}">{{ page }}</a>
                {% endif %}
            {% endfor %}

            <a href="{{ path('category_show', {id: category.id, p: current_page + 1 }) }}">
                <img src="{{ asset('images/next.png') }}" alt="Next page" title="Next page" />
            </a>

            <a href="{{ path('category_show', {id: category.id, p: pages_count }) }}">
                <img src="{{ asset('images/last.png') }}" alt="Last page" title="Last page" />
            </a>
        </div>
    {% endif %}

    <div class="pagination_desc">
        <strong>{{ total_jobs }}</strong> jobs in this category

        {% if pages_count > 1 %}
            - page <strong>{{ current_page }}/{{ pages_count }}</strong>
        {% endif %}
    </div>
```
