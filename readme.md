# SDash (Laravel)

See Laravel docs at [https://laravel.com/docs/master](https://laravel.com/docs/master)

See database schema at [https://dbdiagram.io/](https://dbdiagram.io/)

See REST naming conventions at [https://restfulapi.net/resource-naming/](https://restfulapi.net/resource-naming/)

## Installation

* Create an empty MySQL database and user with full access (I'm using [Laravel Homestead](https://laravel.com/docs/6.x/homestead) for this project, but it's not mandatory)
* Git clone this repository
* In the root directory, run `composer install`
* Copy the env example file `cp .env.example .env` and fill in the database location, user and password as well as personalising the site name and [setting up MailHog](https://laravel.com/docs/6.x/homestead#configuring-mailhog) (if needed)
* In the root directory, run the following commands:
```
$ php artisan migrate
$ npm install
$ npm run dev
```
This will install the database structure and contents, the routes needed for user authentication using [Laravel Sanctum](https://laravel.com/docs/7.x/sanctum) and the front-end resources for the auth pages.

The .env file found in the project route (or created by you from the .env.example file) needs to have these properties filled in, referring to the URL where the VUE application is mounted and the URL for API calls.

```
MIX_DASHBOARD_URL=http://sdash.laravel
MIX_API_URL=http://sdash.laravel/api
MIX_SMART_TAG_URL=https://smtag.sourcedata.io/api/v1/tag

```

For local deployment and development with docker and docker-compose see `docker-compose/docker_help.md`.

### Deployment

Project deployment is in the process of being configured with [Deployer](https://deployer.org/).

Deployer will be installed as a project dependency when you run ```composer install```

You should configure the deploy.php file to contain the correct hostname and deploy path for this project.

Running ```php vendor/bin/dep deploy dev``` will deploy the **dev_server** branch to the host called "dev" in the config file.

The dev_server branch has certain special configurations to permit deploying to the dev server. It has a .env.dev file containing only the variables used by Webpack to configure the Vue application on a server-by-server basis. These have the prefix MIX_. The .env.dev file can look like this, for example:

```
MIX_DASHBOARD_URL=/dashboard/
MIX_API_URL=https://sdash-dev.com/api
MIX_PUBLIC_API_URL=https://sdash-dev.com/public-api
MIX_API_PANEL_URL=https://sdash-dev.com/panel
MIX_SMART_TAG_URL=https://smtag.com/api/v1/tag
```

To compile the javascript ready for deployment to the dev server, use ```npm run devcompile``` then push the results to github on the dev_server branch before running ```dep deploy dev```.

### ImageMagick

To convert PDFs into jpgs, we need the imagemagick library and the PHP module to access it.

```
sudo apt-get update && sudo apt-get install -y imagemagick php-imagick
```

See [https://stackoverflow.com/questions/37599727/php-imagickexception-not-authorized](https://stackoverflow.com/questions/37599727/php-imagickexception-not-authorized) to solve a not authorised error from ImageMagick

Restart php-fpm and nginx



__Database Seeding__

Run `php artisan db:seed` to seed the database with some test data
This includes a demo user with SuperAdmin privileges
* email address: embo_it@embo.org
* password: superadmin

You can completely refresh your database (losing any non-seeded data) by running `php artisan migrate:refresh --seed`

### Unit Testing

This project uses Laravel's own integration of PHPUnit. You should set up a testing database using MySQL in order to resemble the real database.

You should create a .env.testing file in the project root. This should be an exact copy of your .env file except the database details.

For example, the database details in .env.testing could be:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sdash_laravel_testing
DB_USERNAME=test_user
DB_PASSWORD=test_password
```
You would need to create this database and user on your local MySQL installation. Once you've created the empty database, there is no need to run migrations - Laravel will run the migrations when needed for the tests.

To run the test suite:

```
php artisan test
```

See also: [Laravel Testing](https://laravel.com/docs/master/http-tests)

## API Endpoints Needed

## User
* GET /users - get collection of all users (superadmin only)
* GET /user - get current user info
* GET /user/panels - get user panels (including group-accessible)
* GET /user/panels - get user panels (including group-accessible)
* GET /user/panels?page=2 - get paginated panels
* GET /user/groups - get user groups
* PATCH /user/{id} - update user details
* DELETE /user/{id} - delete user (superadmin only)

## Panels
* GET /panels - get public panels
* POST /panel - file upload - create new panel named after image filename
* PUT /panel/{id}/image - file upload, replace image
* PATCH /panel/{id} - update panel data
* DELETE /panel/{id} - delete panel
* GET /panel/{id}/comments - get all comments for panel
* POST /panel/{id}/comment - add new comment to panel
* PATCH /panel/{id}/comment - edit the chosen comment
* GET /panel/{id}/powerpoint - download as Powerpoint
* GET /panel/{id}/pdf - download as PDF
* GET /panel/{id}/original - download as original file
* GET /panel/{id}/dar - download as DAR
* POST /panel/{id}/file - add data file to panel
* GET /panel/{id}/files - list data files for panel
* DELETE /panel/{id}/file/{id} - remove data file from panel
* POST /panel/{id}/tags - add tags to panel
* DELETE /panel/{id}/tag/{id} - remove tag from panel

## Groups
* POST /group - create a new group
* GET /group/{id} - get group details
* POST /group/{id}/users - add users to group
* GET /group/{id}/users - get users in group
* POST /group/{id}/panels - add panels to group
* DELETE /group/{id}/panel/{id} - remove panel from group
* DELETE /group/{id}/user/{id} - remove user from group
* PATCH /group/{id}/user/{id} - modify user's role in group (from user to admin & vice versa)

## Images
* GET /image/{id} - stream the requested image IF the signed-in user has access or the image is public
    Note: move public images into public folder to bypass auth? Lazy load to prevent excessive server load.

## Figures
* POST /figure - create a new figure from the posted panel IDs and other info
* PATCH /figure/{id} - modify the figure contents
* DELETE /figure/{id} - delete figure (but not contents!)
* GET /figure/{id} - return information about figure
* GET /figures - return all figures owned by the user
* GET /figure/{id}/panels - get all panels belonging to figure

# Files
* GET /file/{id} - download the file if it's accessible to user

# Tags
* GET /tag/{id}/panels - get panels by tag

# Fontawesome
See https://www.npmjs.com/package/@fortawesome/vue-fontawesome for instructions on using the FontAwesome library for Vue

e.g.
```html
<font-awesome-icon icon="save" size="lg"/>
<font-awesome-icon icon="trash-alt" size="lg"/>
```

# Bootstrap-Vue
Instructions for the pre-built component library of Bootstrap Components can be found here https://bootstrap-vue.js.org/

# Post deployment notes
The following command should be run once in the project root on the first deployment to populate the file_categories table: ```php artisan db:seed --class=FileCategoriesTableSeeder```

This command should be run to populate the licenses database: ```php artisan db:seed --class=LicensesTableSeeder```

If seeding the database with generated records, or updating the database from an earlier installation, you may need to generate the panel-author relationships. To do so, run the following mysql command:

```sql
insert into panel_user(user_id, panel_id, role, `order`, created_at, updated_at)
select  p.user_id,
p.id as panel_id,
'corresponding' as role,
0 as `order`,
NOW() as created_at,
NOW() as updated_at
from panels p;
```

Note: When a new user registers, the list of external authors is checked for their email address. If anyone has added their address as an external author to a panel, this is converted into a registered author and the newly-registered user is given the author role on the panel.