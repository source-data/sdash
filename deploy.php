<?php

namespace Deployer;

require 'recipe/laravel.php';
require 'recipe/npm.php';

// Project name
set('application', 'SDash');

// Project repository
set('repository', 'git@github.com:source-data/sdash.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

// Shared files/dirs between deploys
add('shared_files', []);
add('shared_dirs', []);

// Writable dirs by web server
add('writable_dirs', []);


// Hosts

host('167.172.111.183')
    ->user('deployer')
    ->set('branch', 'master')
    ->identityFile('~/.ssh/id_rsa.pub')
    ->set('deploy_path', '/var/www/html/sdash.sourcedata.io');

// Tasks

task('npm:compile', function () {
    run('cd {{release_path}} && npm run production');
});

//remove node_modules to save space
task('npm:remove', function () {
    run('cd {{release_path}} && rm -rf node_modules');
});

desc('install and compile npm packages');
after('deploy:update_code', 'npm:install');
after('npm:install', 'npm:compile');
after('npm:compile', 'npm:remove');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.

before('deploy:symlink', 'artisan:migrate');

desc('Restart PHP-FPM service');
task('php-fpm:restart', function () {
    // The user must have rights for restart service
    // /etc/sudoers: username ALL=NOPASSWD:/bin/systemctl restart php-fpm.service
    run('sudo systemctl restart php7.4-fpm.service');
});
after('deploy:symlink', 'php-fpm:restart');
