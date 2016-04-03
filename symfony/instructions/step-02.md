Step 02 - The data model
------------------------

To create the data model for the jobs and categories you need to create the so
called entity classes for the Doctrine ORM. [Read here more](http://symfony.com/doc/current/book/doctrine.html)

### 1. Create the `Category` entity

To create the `Category` entity call the `doctrine:generate:entity` command:

```bash
php bin/console doctrine:generate:entity --entity=AppBundle:Category
```

The first two questions can be answered with `ENTER`. Now you need to add the
`name` field as `string` with length `255` and not nullable but unique.

### 2. Create the `Job` entity

Same as above you use the command:

```bash
php bin/console doctrine:generate:entity --entity=AppBundle:Job
```

and create the following fields:

| name           | type       | length | nullable | unique |
| -------------- | ---------- | ------ | -------- | ------ |
| `type`         | `string`   | `255`  |          |        |
| `company`      | `string`   | `255`  |          |        |
| `logo`         | `string`   | `255`  | `true`   |        |
| `url`          | `string`   | `255`  | `true`   |        |
| `position`     | `string`   | `255`  |          |        |
| `location`     | `string`   | `255`  |          |        |
| `description`  | `text`     |        |          |        |
| `how_to_apply` | `text`     | `255`  |          |        |
| `token`        | `string`   | `255`  |          | `true` |
| `email`        | `string`   | `255`  |          |        |
| `expires_at`   | `datetime` |        |          |        |

### 3. Timestampable Job

You want to know automatically when the job database record was created and
updated the last time. For this you can use a Doctrine extension which
implements this. [Read here more](https://symfony.com/doc/master/bundles/StofDoctrineExtensionsBundle/index.html)

The `StofDoctrineExtensionsBundle` is already installed and configured for this
workshop :)

To enable the timestamable behaviour on the `Job` entity you can simply use a
a `TimestampableEntity` trait. That's all. You could also configure the fields
on your own, but why you should? [Read here more](https://github.com/Atlantic18/DoctrineExtensions/blob/master/doc/timestampable.md)

```php
<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Table(name="job")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\JobRepository")
 */
class Job
{
    use TimestampableEntity;

    // ...
}
```

### 4. Creating the relation between the Job and the Category

As described in the user stories each category can have many categories, so you
have an `OneToMany` relation from category to job and a `ManyToOne` relation
from job to category respectively:

```php
class Job
{
    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="jobs", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(nullable=false)
     * })
     */
    private $category;
}
```

```php
class Category
{
    /**
     * @ORM\OneToMany(targetEntity="Job", mappedBy="category")
     *
     * @var Job[]|Collection
     */
    private $jobs;
}
```

With the `doctrine:generate:entities` command you can add the missing getter
and setter functions

```bash
php bin/console doctrine:generate:entities AppBundle
```

but you need to change the generated `Job::setCategory()` to support the
bidirectional relation in PHP too and set the jobs relation to extra lazy too:

```php
class Job
{
    /**
     * @ORM\OneToMany(targetEntity="Job", mappedBy="category", fetch="EXTRA_LAZY")
     *
     * @var Job[]|Collection
     */
    private $jobs;

    /**
     * @param Category $category
     *
     * @return Job
     */
    public function setCategory(Category $category)
    {
        if (null !== $this->category) {
            $this->category->removeJob($this);
        }

        $this->category = $category;
        $this->category->addJob($this);

        return $this;
    }
}
```

### 5. Create the database

Last but not least you need to create the database schema. Doctrine has a
simple way to do this, but in this example you will use the a migrations
bundle, which helps you to keep updating your database in a sane way without
headaches. [Read more here](http://symfony.com/doc/master/bundles/DoctrineMigrationsBundle/index.html)

The `DoctrineMigrationsBundle` is already installed and configured for this
workshop :)

Now you can call the `doctrine:migrations:diff` command to generate a migration
by comparing your current databse schema (which is empty) with the created
entities:

```bash
php bin/console doctrine:migrations:diff
```

As a result you have a generated php file in your `src/Migrations` directory.
The class name is a timestamp but for better readability and to be consistent
you should rename it to something useful.

For the workshop `Version01DataModel` as class name and `Version01DataModel.php`
as the file name fits perfectly.

To update the database you simply call the `doctrine:migrations:migrate`
command:

```bash
php bin/console doctrine:migrations:migrate
```
