# Setup to run SDash with Docker

This is inspired from https://www.digitalocean.com/community/tutorials/how-to-set-up-laravel-nginx-and-mysql-with-docker-compose

Install docker (https://docs.docker.com/get-docker/) and docker-compose (https://docs.docker.com/compose/install/).

Clone this repository

    git clone git@github.com:source-data/sdash.git
    cd sdash

Update .env.exemple into .env and make sure the following variables are set:

    DB_CONNECTION=mysql
    DB_HOST=db
    DB_PORT=3306
    DB_DATABASE=laravel
    DB_USERNAME=laraveluser
    DB_PASSWORD=dbpass

For local developement:

    APP_URL=http://localhost
    MIX_DASHBOARD_URL=/dashboard/
    MIX_API_URL=http://localhost/api
    MIX_PUBLIC_API_URL=http://localhost/public-api
    MIX_API_PANEL_URL=http://localhost/panel

Save a root password for mysql in `mysql/root_password.txt`

Build the container and run the services:

    docker-compose build
    docker-compose up -d

Install composer

    docker-compose exec app bash
    composer update
    composer install


Grant permission to `laraveluser` in the database:

    docker-compose exec db bash
    mysql -u root -p 
    # root password
    GRANT ALL ON laravel.* TO 'laraveluser'@'%' IDENTIFIED BY 'dbpass';
    FLUSH PRIVILEGES;

Migrate 

    docker-compose exec app bash
    php artisan migrate
    php artisan passport:install

(maybe do docker-compose down and docker-compose up -d again)

Visit http://localhost 