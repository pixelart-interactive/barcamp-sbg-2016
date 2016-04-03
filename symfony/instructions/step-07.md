Step 07: Adding a new job
-------------------------

To create a new job you need to do a bit more :)

### 1. The form type

In Symfony you create forms within form type classes which define the fields of
the form.

Lets create a `JobType` in `src/AppBundle/Form/FormType.php`:

```php
<?php

namespace AppBundle\Form;

use AppBundle\Entity\Job;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Formgit \FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, ['choices' => Job::getTypes(), 'expanded' => true])
            ->add('category')
            ->add('company', TextType::class)
            ->add('url', UrlType::class)
            ->add('position', TextType::class)
            ->add('location', TextType::class)
            ->add('description', TextareaType::class)
            ->add('how_to_apply', TextType::class, ['label' => 'How to apply?'])
            ->add('email', EmailType::class)
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Job',
        ]);
    }
}

```

If you have seen it, the form type uses a static `getTypes()`. Lets create it:

```php
    /**
     * @return string[]
     */
    public static function getTypes()
    {
        return [
            'full-time' => 'Full time',
            'part-time' => 'Part time',
            'freelance' => 'Freelance',
        ];
    }

    /**
     * @return string[]
     */
    public static function getTypeValues()
    {
        return array_keys(self::getTypes());
    }
```

### 2. Create the new action in the controller

To show the form add a `newAction()` to the `JobController`. It is important to
add it before the `showAction()`.

```php
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
```

Now you should call the form with `http://symfony.dev/app_dev.php/job/new`.

### 3. Fix the entity relation error

But actually the page will error, because Symfony tries to fetch the category
relation, but it doesn't know how to render the category.

To fix this you simply add the `__toString()` method, which defines the output
to show in the select box:

```php
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
```

### 4. Style the form

At the moment the provided form template looks ugly. Lets implement a better
view:

```twig
{% extends 'base.html.twig' %}

{% form_theme form _self %}

{% block form_errors %}
    {% spaceless %}
        {% if errors|length > 0 %}
            <ul class="error_list">
                {% for error in errors %}
                    <li>{{ error.messageTemplate|trans(error.messageParameters, 'validators') }}</li>
                {% endfor %}
            </ul>
        {% endif %}
    {% endspaceless %}
{% endblock form_errors %}

{% block stylesheets %}
    {{ parent() }}
    <link rel="stylesheet" href="{{ asset('/css/job.css') }}" type="text/css" media="all"/>
{% endblock %}

{% block content %}
    <h1>Job creation</h1>

    {{ form_start(form) }}
    <table id="job_form">
        <tfoot>
        <tr>
            <td colspan="2">
                <input type="submit" value="Submit your job"/>
            </td>
        </tr>
        </tfoot>
        <tbody>
        <tr>
            <th>{{ form_label(form.category) }}</th>
            <td>
                {{ form_errors(form.category) }}
                {{ form_widget(form.category) }}
            </td>
        </tr>
        <tr>
            <th>{{ form_label(form.type) }}</th>
            <td>
                {{ form_errors(form.type) }}
                {{ form_widget(form.type) }}
            </td>
        </tr>
        <tr>
            <th>{{ form_label(form.company) }}</th>
            <td>
                {{ form_errors(form.company) }}
                {{ form_widget(form.company) }}
            </td>
        </tr>
        <tr>
            <th>{{ form_label(form.url) }}</th>
            <td>
                {{ form_errors(form.url) }}
                {{ form_widget(form.url) }}
            </td>
        </tr>
        <tr>
            <th>{{ form_label(form.position) }}</th>
            <td>
                {{ form_errors(form.position) }}
                {{ form_widget(form.position) }}
            </td>
        </tr>
        <tr>
            <th>{{ form_label(form.location) }}</th>
            <td>
                {{ form_errors(form.location) }}
                {{ form_widget(form.location) }}
            </td>
        </tr>
        <tr>
            <th>{{ form_label(form.description) }}</th>
            <td>
                {{ form_errors(form.description) }}
                {{ form_widget(form.description) }}
            </td>
        </tr>
        <tr>
            <th>{{ form_label(form.how_to_apply) }}</th>
            <td>
                {{ form_errors(form.how_to_apply) }}
                {{ form_widget(form.how_to_apply) }}
            </td>
        </tr>
        <tr>
            <th>{{ form_label(form.email) }}</th>
            <td>
                {{ form_errors(form.email) }}
                {{ form_widget(form.email) }}
            </td>

        </tr>
        </tbody>
    </table>
    {{ form_end(form) }}
{% endblock %}
```

And link the button in the header of `base.html.twig` to the form:

```twig
<a href="{{ path('job_new') }}">Post a Job</a>
```

### 5. Save the submitted form

Maybe you have already tried it, when saving the form it won't work.

To fix this, you must set the values for token and expired at on the pre persist
of the job. To do so add the following methods to the `Job` entity and add the
`HasLifecycleCallbacks` annotation on the class:




