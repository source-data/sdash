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
$ php artisan passport:install
$ npm install
$ npm run dev
```
This will install the database structure and contents, the routes needed for user authentication using Laravel Passport and the front-end resources for the auth pages.

The .env file found in the project route (or created by you from the .env.example file) needs to have these properties filled in, referring to the URL where the VUE application is mounted and the URL for API calls.

```
MIX_DASHBOARD_URL=http://sdash.laravel
MIX_API_URL=http://sdash.laravel/api
MIX_SMART_TAG_URL=https://smtag.sourcedata.io/api/v1/tag

```

### Deployment

Project deployment is in the process of being configured with [Deployer](https://deployer.org/).

To be finalised.

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
