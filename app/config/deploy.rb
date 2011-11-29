set :application, "Tropaion"
# set :serverName,  "pv168.ncsrv.de" # The server's hostname
set :domain,      "pv168.ncsrv.de" # IP address / domain name
set :deploy_to,   "/var/www/web0/tropaion" # Remote location where the project will be stored

set :user,        "root"
set :use_sudo,    false
ssh_options[:port] = 22

# https://github.com/capistrano/capistrano/wiki/Roles
role :web, domain
role :app, domain
role :db,  domain, :primary => true


set :repository,   "git://github.com/Tobion/Tropaion.git"
#set :repository,   "file:///E:/Projekte/Tropaion"
set :scm,          :git
set :branch,       "master"

set :deploy_via,   :remote_cache

#set :deploy_via,   :copy
#set :deploy_via,  :rsync_with_remote_cache
#set :copy_exclude, [".git", ".gitignore"]
#set :copy_dir,     "C:/Temp/Tropaion"


set :model_manager, "doctrine"
set :keep_releases,  3 # The number of releases which will remain on the server

# Set some paths to be shared between versions
set :shared_files,        ["app/config/parameters.ini"]
set :shared_children,     [app_path + "/logs", web_path + "/uploads", "vendor", "src/Tobion/TropaionBundle/Data", "src/Tobion/TropaionBundle/RatingSystem"]

# Update vendors during the deploy
set :update_vendors, true
# run bin/vendors script in mode (upgrade, install (faster if shared /vendor folder) or reinstall)
set :vendors_mode, :install