# Setup to run SDash with Docker

This is inspired from https://www.digitalocean.com/community/tutorials/how-to-set-up-laravel-nginx-and-mysql-with-docker-compose

Install docker (https://docs.docker.com/get-docker/) and docker-compose (https://docs.docker.com/compose/install/).

Clone this repository

    git clone git@github.com:source-data/sdash.git
    cd sdash

Update `.env.exemple` into `.env` and make sure the following variables are set:

    # For local developement!
    APP_URL=http://localhost
    MIX_DASHBOARD_URL=/dashboard/
    MIX_API_URL=http://localhost/api
    MIX_PUBLIC_API_URL=http://localhost/public-api
    MIX_API_PANEL_URL=http://localhost/panel

    # mysql database
    DB_CONNECTION=mysql
    DB_HOST=db
    DB_PORT=3306
    # this needs to correspond to what is in docker-compose.yml !
    DB_DATABASE=laravel
    # this will be used to grant manually priviledge when setting up first time
    DB_USERNAME=laraveluser
    DB_PASSWORD=dbpass

Note that `DB_PASSWORD` is the password for the `laravel` database and NOT the root password. `DB_HOST` is `db` because this is the name of the service within the container as defined in `docker-compose.yml`.

Save the root password for mysql in `mysql/root_password.txt` The file is used to define the secret `db_pwd` in `docker-compose.yml` which is securly transmitted to the `mysql` container.

The main Dockerfile uses the `php:7.4-fpm` image. Essential packages and PHP extensions are installed.
The  local working directory is copied into the container's `/var/www` whcih is the working directory in the container.
A user www is created, port 9000 exposed and the `php-fpm` is run as last command by default.

A second Dockerfile is in `frontend-docker/` to run the installation of the Node modules. 

The docker-compose.yml file sets up 4 services:

- `app`: the main App build with the main Dockerfile. Binds mounts `./php/local.ini`
- `webserver`: nginx server. Mount binds `./nginx/conf.d/` where server configuration is specified, in particular the root of the served files as `root /var/www/public`
- `db`: the mysql database. Uses the `laravel` database, bind mounts `./mysql/my.cnf` and uses secrets to access the root password in mysql/root_password.txt . The data is in permanent named volume `dbdata`.
- `frontend`: uses frontend-docker/Dockerfile and uses an anonymous volume `/app/node_modules` (path exists only in container). The commands `npm run watch` to allow recompile components as soon as the code is changed in development.


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

Grant permission to `laraveluser` for the `laravel` database:

    docker-compose exec db bash
    mysql -u root -p 
    # root password
    GRANT ALL ON laravel.* TO 'laraveluser'@'%' IDENTIFIED BY 'dbpass';
    FLUSH PRIVILEGES;

Install composer and migrate 

    composer update
    composer installdocker-compose exec app bash
    php artisan migrate
    php artisan passport:install

(maybe do `docker-compose down` and `docker-compose up -d` again)

Visit http://localhost

To visualize the database schema:

    docker-compose exec db mysqldump --no-data -u laraveluser -pdbpass laravel > dump.sql

Import `dump.sql` into https://dbdiagram.io/d to visualize the tables and relationships.
