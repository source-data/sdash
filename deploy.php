<?php

namespace Deployer;

require 'recipe/laravel.php';

// Project name
set('application', 'SDash');

// Project repository and branch
set('repository', 'git@github.com:source-data/sdash.git');
set('branch', 'master');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

// No shared files/dirs between deploys
add('shared_files', []);
add('shared_dirs', []);

// No writable dirs by web server
add('writable_dirs', []);

// Deployment path
set('deploy_path', '/var/www/html/sdash.sourcedata.io');

// Hosts
host('dev')
    ->hostname('sdash-dev.sourcedata.io')
    ->stage('dev')
    ->user('deployer');
set('default_stage', 'dev');

// Tasks

// fetch frontend resources from github
task('frontend:fetch', function () {
    run('cd {{release_path}} && .github/scripts/gh-dl-release.sh source-data/sdash dev public.tgz && rm -rf public/ && tar -xvf public.tgz && rm public.tgz');
});

// stack together preparatory tasks
task('sdash:installations', [
    'frontend:fetch',
    'artisan:migrate',
    'artisan:storage:link',
]);

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.
before('deploy:symlink', 'sdash:installations');

desc('Restart PHP-FPM service');
task('php-fpm:restart', function () {
    // The user must have rights for restart service
    // /etc/sudoers: username ALL=NOPASSWD:/bin/systemctl restart php-fpm.service
    run('sudo systemctl restart php7.4-fpm.service');
});
after('deploy:symlink', 'php-fpm:restart');
