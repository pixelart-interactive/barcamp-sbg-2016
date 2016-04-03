Step 06: The job detail page
----------------------------

To show the job detail it's rather simple.

### 1. Create the controller action

Add the following `showAction()` to the `JobController`:

```php
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
```

### 2. The repository method

If you have recognised we define our own repository method to fetch the active
job only, otherwise a 404 is thrown.

Lets create the `findActiveJob()` method in the `JobRepository`:

```php
    /**
     * @param int $id
     *
     * @return Job
     */
    public function findActiveJob($id)
    {
        $qb = $this->createQueryBuilder('job')
            ->where('job.id = :id')
            ->andWhere('job.expiresAt > CURRENT_TIMESTAMP()')
            ->setParameter('id', $id)
        ;

        return $qb->getQuery()->getSingleResult();
    }
```

### 3. The view

Add the `job/show.html.twig` view:

```twig
{% extends 'base.html.twig' %}

{% block title %}
    {{ job.company }} is looking for a {{ job.position }}
{% endblock %}

{% block stylesheets %}
    {{ parent() }}
    <link rel="stylesheet" href="{{ asset('css/job.css') }}" type="text/css" media="all" />
{% endblock %}

{% block content %}
    <div id="job">
        <h1>{{ job.company }}</h1>
        <h2>{{ job.location }}</h2>
        <h3>
            {{ job.position }}
            <small> - {{ job.type }}</small>
        </h3>

        {% if job.logo %}
            <div class="logo">
                <a href="{{ job.url }}">
                    <img src="{{ asset('uploads/logo/' ~ job.logo) }}" alt="{{ job.company }} logo" />
                </a>
            </div>
        {% endif %}

        <div class="description">
            {{ job.description|nl2br }}
        </div>

        <h4>How to apply?</h4>

        <p class="how_to_apply">{{ job.howToApply }}</p>

        <div class="meta">
            <small>posted on {{ job.createdAt|date('m/d/Y') }}</small>
        </div>
    </div>
{% endblock %}
```

### 4. Link to the job page

Change the link in the `job/_jobs.html.twig` partial to the real job page:

```twig
{{ path('job_show', {id: job.id}) }}
```

Now you should be able to call the job detail page from the homepage and the
category listings.
