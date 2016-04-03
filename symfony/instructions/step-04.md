Step 04: Templating with Twig
-----------------------------

The list looks ugly, it must be designed.

### 1. Add the overview template

You can modify the template for the job list in `app/Resources/views/jobs/index.html.twig`:

```twig
{% extends 'base.html.twig' %}

{% block stylesheets %}
    {{ parent() }}
    <link rel="stylesheet" href="{{ asset('css/jobs.css') }}" type="text/css" media="all" />
{% endblock %}

{% block content %}
    <div id="jobs">
        {% for category in categories %}
            <div>
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
            </div>
        {% endfor %}
    </div>
{% endblock %}
```

### 2. Add the base layout

The main layout every page shares is to modify in `app/Resources/views/base.html.twig`:

```twig
<!DOCTYPE html>
<html>
<head>
    <title>
        {% block title %}
            Jobeet - Your best job board
        {% endblock %}
    </title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    {% block stylesheets %}
        <link rel="stylesheet" href="{{ asset('css/main.css') }}" type="text/css" media="all"/>
    {% endblock %}
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}"/>
</head>
<body>
<div id="container">
    <div id="header">
        <div class="content">
            <h1>
                <a href="{{ path('job_index') }}">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Jobeet Job Board"/>
                </a>
            </h1>

            <div id="sub_header">
                <div class="post">
                    <h2>Ask for people</h2>
                    <div>
                        <a href="#">Post a Job</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="content">
        {% for flashMessage in app.session.flashbag.get('notice') %}
            <div class="flash_notice">
                {{ flashMessage }}
            </div>
        {% endfor %}

        {% for flashMessage in app.session.flashbag.get('error') %}
            <div class="flash_error">
                {{ flashMessage }}
            </div>
        {% endfor %}

        <div class="content">
            {% block content %}
            {% endblock %}
        </div>
    </div>

    <div id="footer">
        <div class="content">
            <span class="symfony">
                <img src="{{ asset('images/jobeet-mini.png') }}"/>
                    powered by <a href="http://www.symfony.com/">
                    <img src="{{ asset('images/symfony.png') }}" alt="Symfony" style="height: 20px;"/>
                </a>
            </span>
            <ul>
                <li><a href="">About Jobeet</a></li>
            </ul>
        </div>
    </div>
</div>
{% block javascripts %}
{% endblock %}
</body>
</html>
```
