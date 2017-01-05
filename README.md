# Agora Status UI (Cachet based)

Agora Status UI is an Upwork project forked from [Cachet](https://docs.cachethq.io/docs) (a beautiful and powerful open source status page system, built using the [Laravel](https://laravel.com) framework).

## Requirements

- Git
- PHP 5.5.9+ or newer
- HTTP server with PHP support (eg: Apache, Nginx, Caddy)
- DBMS (MySQL, PostgreSQL, SQLite)
- [Composer](https://getcomposer.org)

## Installing Agora Status UI

For installing the project, it's needed to clone it and just follow the regular Cachet installation steps.

1. Clone the Agora Status UI project from the [Stash repository](https://stash.odesk.com/projects/AGORA/repos/agora-status-cachet/browse)
2. [Install Cachet](https://docs.cachethq.io/docs/installing-cachet)

## Upgrading Agora Status UI

Currently, the project is deployed in the next hosts, using PHP7, Apache and PostgreSQL:

- Development environment: [http://agora-status.dev.agora.odesk.com/](http://agora-status.dev.agora.odesk.com/)
- Production environment: [http://agora-status.prod.agora.odesk.com/](http://agora-status.prod.agora.odesk.com/)

For upgrading the project just:

1. Login into the host
2. Go to /var/www/Cachet, the path where the project is deployed
3. Pull your changes
4. Follow the instructions for [upgrading Cachet](https://docs.cachethq.io/docs/updating-cachet)

## Cachet Documentation

Documentation is found at [https://docs.cachethq.io/docs](https://docs.cachethq.io/docs).
