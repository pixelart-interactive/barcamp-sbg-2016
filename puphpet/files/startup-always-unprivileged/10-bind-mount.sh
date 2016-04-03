#!/usr/bin/env bash

mkdir -p /run/shm/symfony/var/{cache,logs,sessions}

sudo mount --bind /run/shm/symfony/var/cache /var/www/barcamp/symfony/var/cache
sudo mount --bind /run/shm/symfony/var/logs /var/www/barcamp/symfony/var/logs
sudo mount --bind /run/shm/symfony/var/sessions /var/www/barcamp/symfony/var/sessions
