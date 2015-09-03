## System
CREATE USER 'vmpanel'@'localhost' IDENTIFIED BY 'cvSvBGUvKRdECtfN';GRANT USAGE ON *.* TO 'vmpanel'@'localhost' IDENTIFIED BY '***' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;CREATE DATABASE IF NOT EXISTS `vmpanel`;GRANT ALL PRIVILEGES ON `vmpanel`.* TO 'vmpanel'@'localhost';
INSERT INTO `vmpanel`.`user` (`id`, `username`, `email`, `password_hash`, `auth_key`, `confirmed_at`, `unconfirmed_email`, `blocked_at`, `registration_ip`, `created_at`, `updated_at`, `flags`) VALUES (NULL, 'admin', 'support@serveur-express.com', '$2y$13$UKX8bZZOnOHsoQxRTZm4MOn.F61lK1zMs5h.GSwXWTuPS/mqXRNua', REVERSE('lppgqpAuTIX1YT9sMk-Yig4QDGw7e8dd'), '1418745915', NULL, NULL, NULL, '1418745915', '1418745915', '0');
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

## Update
cd /home/vmpanel/
git pull
composer update