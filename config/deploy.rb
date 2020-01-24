# config valid only for current version of Capistrano
lock "3.11.2"

set :application, "sdash.sourcedata.io"
set :repo_url, "git@git.embl.de:pewter/sdash-laravel.git"

# Default branch is :master
# ask :branch, `git rev-parse --abbrev-ref HEAD`.chomp
set :branch, 'master'

# Default value for :format is :airbrussh.
# set :format, :airbrussh

# You can configure the Airbrussh format using :format_options.
# These are the defaults.
# set :format_options, command_output: true, log_file: "log/capistrano.log", color: :auto, truncate: :auto

# Default value for :pty is false
# set :pty, true

# Default value for :linked_files is []
append :linked_files, ".env"

# Default value for linked_dirs is []
append :linked_dirs, "vendor", "storage"

# Default value for default_env is {}
# set :default_env, { path: "/opt/ruby/bin:$PATH" }

# Default value for keep_releases is 5
set :keep_releases, 3


# Which roles to consider as laravel roles
set :laravel_roles, :all

# The artisan flags to include on artisan commands by default
# set :laravel_artisan_flags, "--env=#{fetch(:stage)}"
set :laravel_artisan_flags, "--env=production"

# Which roles to use for running migrations
set :laravel_migration_roles, :all

# The artisan flags to include on artisan commands by default when running migrations
# set :laravel_migration_artisan_flags, "--force --env=#{fetch(:stage)}"
set :laravel_migration_artisan_flags, "--force --env=production"

# The version of laravel being deployed
set :laravel_version, 6.0

# Whether to upload the dotenv file on deploy
set :laravel_upload_dotenv_file_on_deploy, false

# Which dotenv file to transfer to the server
# set :laravel_dotenv_file, './.env'

# The user that the server is running under (used for ACLs)
set :laravel_server_user, 'www-data'

# Ensure the dirs in :linked_dirs exist?
set :laravel_ensure_linked_dirs_exist, true

# Link the directores in laravel_linked_dirs?
set :laravel_set_linked_dirs, true

# Linked directories for a standard Laravel 5 application
set :laravel_5_linked_dirs, [
  'storage'
]

# Ensure the paths in :file_permissions_paths exist?
set :laravel_ensure_acl_paths_exist, true

# Set ACLs for the paths in laravel_acl_paths?
set :laravel_set_acl_paths, true

# Paths that should have ACLs set for a standard Laravel 5 application
set :laravel_5_acl_paths, [
  'bootstrap/cache',
  'storage',
  'storage/app',
  'storage/app/public',
  'storage/framework',
  'storage/framework/cache',
  'storage/framework/sessions',
  'storage/framework/views',
  'storage/logs'
]

# set :npm_target_path, -> { release_path.join('subdir') } # default not set
set :npm_flags, '' # '--production --silent --no-progress'    # default
set :npm_roles, :all                                     # default
set :npm_env_variables, {}                               # default
set :npm_method, 'install'                               # default

# set correct permisions
# set :file_permissions_paths, ["storage", "vendor", "node_modules"]
# set :file_permissions_groups, ["deploy"]
# set :file_permissions_users, ["deploy"]
# set :file_permissions_chmod_mode, "0775"
# before "deploy:updated", "deploy:set_permissions:chmod"
# before "deploy:updated", "deploy:set_permissions:chown"
# before "deploy:updated", "deploy:set_permissions:chgrp"



after "deploy:updated", "laravel:migrate"

after "deploy:updated", "compile_assets" do
  # Rake::Task["npm:run"].invoke("production")
  on roles [:app] do
    within release_path do
      execute :npm, 'run', "production"#, "--production --silent --no-progress"
    end
  end
end

# before "compile_assets", "HACK_LARAVEL_link_webpack.mix.js" do
#   on roles [:app] do
#     within release_path do
#       execute :ln, '-sf', release_path.join("webpack.mix.js"), shared_path.join("webpack.mix.js")#, "--production --silent --no-progress"
#     end
#   end
# end

# after "deploy:published", "deploy:restart_php_fpm" do
#   on roles [:app] do
#     # In order for this to work remember to add an entry for user deploy in sudoers:
#     # => https://stevegrunwell.com/blog/restart-php-fpm-during-deployments/
#     # => visudo
#     # => deploy ALL=NOPASSWD: /usr/sbin/service php7.0-fpm restart
#     # => under "# User privilege specification:"
#     execute "sudo service php7.3-fpm restart"
#   end
# end

# after "deploy:published", "deploy:notify_airbrake" do
#   on roles [:app] do
#     project_id  = ENV['AIRBRAKE_PROJECT_ID']
#     project_key = ENV['AIRBRAKE_PROJECT_KEY']
#     environment = fetch(:stage)
#     repository  = 'https://git.embl.de/mainar/embo-it-panel'
#     revision    = `git rev-parse HEAD`.strip
#     username    = `whoami`.strip

#     cmd = %[curl -X POST -H "Content-Type: application/json" -d '{"environment":"#{environment}","username":"#{username}","repository":"#{repository}","revision":"#{revision}"}' "https://airbrake.io/api/v4/projects/#{project_id}/deploys?key=#{project_key}"]

#     execute "#{cmd} 2> /dev/null"
#   end
# end



# namespace :npm do
#   desc "run a npm command"
#   task :run, [:command_name] do |_t, args|
#     command = args[:command_name]
#     on roles fetch(:npm_roles) do
#       within fetch(:npm_target_path, release_path) do
#         with fetch(:npm_env_variables, {}) do
#           execute :npm, 'run', command, *args.extras#, fetch(:npm_flags)
#         end
#       end
#     end
#   end
# end
