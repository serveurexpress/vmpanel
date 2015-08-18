## System
mkdir /etc/vmpanel
sqlite3 /etc/vmpanel/vmpanel.db

apt-get install nginx php5-fpm
vi /etc/nginx/sites-available/default

server {
	listen 8080 default_server;
	listen [::]:8080 default_server;

	root /var/www/vmpanel/web;

	server_name _;

	location / {
		try_files $uri $uri/ =404;
	}
	
	location ~ \.php$ {
    		try_files $uri =404;
    		fastcgi_pass   unix:/var/run/php5-fpm.sock;
    		fastcgi_index  index.php;
    		fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    		include fastcgi_params;
	}

	location ~ /\.ht {
		deny all;
	}
}

ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default
/etc/init.d/nginx restart

curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

## Install
cd /var/www/
git clone https://proj.alphaweb.fr/thomas/vmpanel.git
cd /var/www/vmpanel/
composer install
chown -R www-data runtime && chown -R www-data web/assets/

## Update
cd /var/www/vmpanel/
git pull
composer update