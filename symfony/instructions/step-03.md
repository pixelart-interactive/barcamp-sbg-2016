Step 03: The controller and the categorized list
------------------------------------------------

To output the jobs you need to create a controller with the index action and
get the jobs by category.

### 1. Add the controller

In your `AppBundle` delete the `DefaultController.php` in the `Controller`
directory and add a `JobController.php` with the `indexAction()`:

```php
<?php

namespace AppBundle\Controller;

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

        $jobs = $em->getRepository('AppBundle:Job')->findAll();

        return $this->render('job/index.html.twig', [
            'jobs' => $jobs,
        ]);
    }
}
```

If you visit `http://symfony.dev/app_dev.php/` you should see an empty job
list. I provided you a generated index template for the moment.

### 2. Adding dummy data with fixtures

With a fixtures library you can import dummy or test data into database. So you
have a set of data to develop with and you don't need to care about already to
add a form for your jobs and categories. [Read more here](https://github.com/nelmio/alice)

The `knplabs/rad-fixtures-load` is already installed and configured for this
workshop :) It uses additionally a faker to generate random text.

Add a `category.yml` to the `src/AppBundle/Resources/fixtures/orm` directory:

```yaml
AppBundle\Entity\Category:
    category{1..4}:
        name (unique): <(ucwords($fake('words', null, 2, true)))>
```

And a `job.yml`:

```yaml
AppBundle\Entity\Job:
    job{1..500}:
        type: <randomElement(['full-time', 'part-time', 'freelance'])>
        category: '@category*'
        company: <de_AT:company()>
        logo: 50%? <image('web/uploads/logo', 170, 100, 'abstract', false)>
        url: 80%? <de_AT:url()>
        position: <catchPhrase()>
        location: <de_AT:cityName()>
        description: <realText(300, 3)>
        howToApply: <sentence()>
        token (unique): <uuid()>
        email: <de_AT:email()>
        createdAt: <dateTimeBetween('-70 days', 'now')>
        updatedAt: <dateTimeBetween($createdAt, 'now')>
        expiresAt: <((clone $createdAt)->add(new \DateInterval('P30D')))>
```

Import the data now with the `rad:fixtures:load` command:

```bash
php bin/console rad:fixtures:load
```

### 3. Categorize the jobs

To have the jobs blocked by category you must add a custom method to the
category repository. First modify the `src/AppBundle/Repository/CategoryRepository.php`
and add the `findAllCategoriesWithNewestJobs()` method:

```php
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
```

The method will only return the categories which have active jobs. To get the
newest 10 active jobs for each category, you need a `findActiveJobs()` method
on the `JobRepository`:

```php
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
```

You don't want to override the `$jobs` in the category to set the active jobs,
so you create a `$activeJobs` in the `Category`:

```php
    /**
     * @var Job[]|Collection
     */
    private $activeJobs;

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
```

Those methods should be called in the controller index action, so modify it
accordingly:

```php
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
```

And fix the view to list the categories:

```twig
{% extends 'base.html.twig' %}

{% block body %}
    {% for category in categories %}
        <h1>{{ category.name }}</h1>

        <table>
            <thead>
            <tr>
                {# The table head stuff #}
            </tr>
            </thead>
            <tbody>
            {% for job in category.activeJobs %}
                <tr>
                    {# The table data stuff #}
                </tr>
            {% endfor %}
            </tbody>
        </table>
    {% endfor %}
{% endblock %}
```

That's it.
