Step 01 - Create a Symfony project
----------------------------------

At first we should setup a Symfony project at all.

### 1. Obtain the installer

Symfony has it's own installer command, so we must get it:

```bash
sudo curl -LsS https://symfony.com/installer -o /usr/local/bin/symfony
sudo chmod a+x /usr/local/bin/symfony
```

If it does not download, it's no problem, the installer is pre installed.

### 2. Create the project

We create us a test project:

```bash
symfony new testproject
```

### 3. Start the internal webserver

Now we can change into the test project and start the internal php webserver:

```bash
cd /var/www/barcamp/testproject
php bin/console server:run 0.0.0.0:8000
```

Symfony should now be accessible through `http://symfony.dev:8000`.

### 4. Delete it ;)

Since we supplied our preconfigured project for the workshop you can delete the
test project now. First hit `Ctrl+C` to terminate the internal webserver.

```bash
cd /var/www/barcamp
rm -rf testproject
```

### 5. Install dependencies

Our supplied code base is in the Git, thus there are no dependencies installed
(never commit vendor code!). So we must install it with `composer`:

```bash
cd /var/www/barcamp/symfony
composer install
```

Now you should see the same output as before if you visit
`http://symfony.dev/app_dev.php`.
