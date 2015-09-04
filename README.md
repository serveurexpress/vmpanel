## System
apt-get install nginx php5-fpm at sudo php5-curl php5-intl
vi /etc/nginx/sites-available/vmpanel

server {
	listen 8080 default_server;
	listen [::]:8080 default_server;

	root /home/vmpanel/web;

        index index.php;

	server_name _;

	location / {
		try_files $uri $uri/ /index.php$is_args$args;
	}
	
	location ~ \.php$ {
    		try_files $uri =404;
    		fastcgi_pass   unix:/var/run/php5-fpm.sock;
    		fastcgi_index  index.php;
    		fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    		include fastcgi_params;
	}

	location ~ /\.(ht|svn|git) {
		deny all;
	}
}

ln -s /etc/nginx/sites-available/vmpanel /etc/nginx/sites-enabled/vmpanel
/etc/init.d/nginx restart

curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
composer global require "fxp/composer-asset-plugin:~1.0.3"

## Install
cd /home/
git clone https://proj.alphaweb.fr/thomas/vmpanel.git
cd /home/vmpanel/
composer install
chown -R www-data runtime && chown -R www-data web/assets/
vi /etc/sudoers
Cmnd_Alias STARTVM=/home/kvm/istartVM
Cmnd_Alias STOPVM=/home/kvm/stopVM
Cmnd_Alias STATUSVM=/home/kvm/status
Cmnd_Alias FSCKVM=/home/kvm/fsckVM
Cmnd_Alias AT=/usr/bin/at
www-data ALL= NOPASSWD: ADDSSH,VQLOG,VERSION,STARTVM,STOPVM,STATUSVM,FSCKVM,AT
mysql -u root -p < /home/vmpanel/sql/vmpanel_user_mysql.sql
mysql -u root -p vmpanel < /home/vmpanel/sql/vmpanel_mysql.sql

## Update
cd /home/vmpanel/
git pull
composer update