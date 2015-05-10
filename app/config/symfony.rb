before 'symfony:composer:install', 'composer:copy_vendors'
before 'symfony:composer:update', 'composer:copy_vendors'

after 'symfony:composer:install', 'composer:dump_autoload'
after 'symfony:composer:update', 'composer:dump_autoload'

namespace :composer do
    task :copy_vendors, :except => { :no_release => true } do
        capifony_pretty_print "--> Copy vendor file from previous release"

        run "vendorDir=#{current_path}/vendor; if [ -d $vendorDir ] || [ -h $vendorDir ]; then cp -a $vendorDir #{latest_release}; fi;"
        capifony_puts_ok
    end

    task :dump_autoload, :except => { :no_release => true } do
        run "cd #{latest_release} && #{php_bin} composer.phar dump-autoload -o"
    end
end
