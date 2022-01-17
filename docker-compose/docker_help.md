# Setup to run SDash with Docker

This is inspired from https://www.digitalocean.com/community/tutorials/how-to-set-up-laravel-nginx-and-mysql-with-docker-compose

[Install docker](https://docs.docker.com/get-docker/) (and [docker-compose](https://docs.docker.com/compose/install/), if it's not included in your version of docker).

Clone this repository

    git clone git@github.com:source-data/sdash.git
    cd sdash

Update `.env.example` into `.env` and make sure the following variables are set:

    # For local developement!
    APP_URL=http://localhost:8080
    MIX_DASHBOARD_URL=/dashboard/
    MIX_API_URL=http://localhost/api
    MIX_API_PANEL_URL=http://localhost/panel
    SANCTUM_STATEFUL_DOMAINS='localhost:8080'

    # mysql database
    DB_CONNECTION=mysql
    DB_HOST=db
    DB_PORT=3306
    # this corresponds to what is in docker-compose.yml
    DB_DATABASE=laravel
    DB_USERNAME=laraveluser
    DB_PASSWORD=dbpass

The main Dockerfile uses the `php:7.4-fpm` image. Essential packages and PHP extensions are installed.
The  local working directory is mounted into the container's `/var/www` which is the working directory in the container.
A user www is created, port 9000 exposed and the `php-fpm` is run as last command by default.

A second Dockerfile is in `frontend/` to run the installation of the Node modules. 

The docker-compose.yml file sets up 4 services:

- `app`: the main App build with the main Dockerfile. Binds mounts `./php/local.ini`
- `webserver`: nginx server. Mount binds `./nginx/conf.d/` where server configuration is specified, in particular the root of the served files as `root /var/www/public`.
- `db`: the mysql database. Uses the `laravel` database and bind mounts `./mysql/my.cnf`. The data is in permanent named volume `dbdata`.
- `frontend`: uses frontend/Dockerfile and uses an anonymous volume `/app/node_modules` (path exists only in container). The commands `npm run watch` to allow recompile components as soon as the code is changed in development.

To create accounts in SDash, email verification is essential. Make sure to set MAIL variables in .env. 
For me it was:

    MAIL_DRIVER=smtp
    MAIL_HOST=<...>
    MAIL_PORT=465
    MAIL_USERNAME=<...>
    MAIL_PASSWORD=<...>
    MAIL_ENCRYPTION=ssl

Build the container and run the services:

    docker-compose build
    docker-compose up -d

Install dependendencies, migrate and seed the database:

    docker-compose exec app composer update && composer install
    docker-compose exec frontend npm install
    docker-compose exec app php artisan migrate
    docker-compose exec app php artisan db:seed --class=FileCategoriesTableSeeder
    docker-compose exec app php artisan db:seed --class=LicensesTableSeeder

Finally, generate an application encryption key:

    docker-compose exec app php artisan key:generate

(maybe do `docker-compose down` and `docker-compose up -d` again)

Visit http://localhost:8080

To visualize the database schema:

    docker-compose exec db mysqldump --no-data -u laraveluser -pdbpass laravel > dump.sql

Import `dump.sql` into https://dbdiagram.io/d to visualize the tables and relationships.
