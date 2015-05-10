load File.dirname(__FILE__) + '/symfony'

# configure global settings
set :application,           "whatisurl"
set :domain,                "http://whatisurl.net"

set :app_path,              "app"

set :repository,            "git@192.168.0.2:php/whatisurl.git"
set :scm,                   :git
set :deploy_via,            :copy
set :copy_exclude,          ".git"

set :ssh_options,           {:forward_agent => true, :paranoid => false}
set :use_sudo,              false
set :keep_releases,         3

# Be more or less verbose by commenting/uncommenting the following lines
#logger.level = Logger::IMPORTANT
logger.level = Logger::INFO
#logger.level = Logger::DEBUG
#logger.level = Logger::TRACE
#logger.level = Logger::MAX_LEVEL

set :shared_children,       [web_path + "/uploads"]
set :shared_files,          ["app/config/parameters.yml", "front/src/javascript/config.js"]
set :clear_controllers,     false

# do not install assets with Capifony, Composer already does this job
set :assets_install,        false
set :dump_assetic_assets,   false
set :use_composer,          true

set :composer_options, "--optimize-autoloader"

before "deploy", "check_releases"
before "deploy:update_code", "check_releases"

after 'symfony:composer:install', 'front:copy_node_modules'
after 'symfony:composer:install', 'front:install'
after 'symfony:composer:install', 'front:publish'

task :check_releases, :roles => :app do
    local_releases = capture("ls -xt #{releases_path}").split.reverse
    releases_count = local_releases.length

    if releases_count > keep_releases
        logger.important "Please run \"cap #{stage} deploy:cleanup\" before deploying (#{releases_count} releases already deployed)"

        exit
    end
end

desc "Deploy to dev instance (http://whatisurl.net)"
task :prod do
    set :stage,  "prod"
    set :branch, "master"

    set :deploy_to, "/var/www/whatisurl"

    set :supervisor_login,    "root"
    set :supervisor_password, "root"

    role :app, "www-data@192.168.0.2", :primary => true
    role :web, "www-data@192.168.0.2"
end

after "deploy:setup" do
    run "if [ ! -d #{deploy_to}/shared/app/config ]; then mkdir -p #{deploy_to}/shared/app/config; fi"

    upload(
        '%s/parameters_%s.yml' % [File.dirname(__FILE__), fetch(:stage)],
        '%s/shared/app/config/parameters.yml' % fetch(:deploy_to)
    )
end

# Front tasks
namespace :front do
    task :copy_node_modules, :except => { :no_release => true } do
        capifony_pretty_print "--> Copy node modules file from previous release"

        run "modulesDir=#{current_path}/front/node_modules; if [ -d $modulesDir ] || [ -h $modulesDir ]; then cp -a $modulesDir #{latest_release}/front; fi;"

        capifony_puts_ok
    end

    task :install, :except => { :no_release => true } do
        capifony_pretty_print "--> Install node modules"

        stream "cd #{latest_release}/front && npm install"
    end

    task :publish, :except => { :no_release => true } do
        capifony_pretty_print "--> Publish assets"

        stream "cd #{latest_release} && make assets-install"
    end
end
