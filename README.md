xBoard
=======================

xBoard is a Anonymous forum written in PHP.  It is
extremely lightweight and use a flat file system
for all of its file information. Currently it is
an ongoing project.

Current Version 0.0.2


Installation
=======================

Upload all the files in the git repo to your server
Change files in xboard/config.php

nginx specific settings
-----------------------
for your site settings, use the code below
    server{

            listen   80;

            root /srv/www/example.com;
            index index.html index.htm index.php xboard.php;
            # Make site accessible from http://localhost/
            server_name www.example.com example.com;

            location / {
                    try_files $uri $uri/ index.html xboard.php;
            }

            location ~ \.php$ {
                    try_files $uri $uri/ /xboard.php;
                    fastcgi_split_path_info ^(.+\.php)(/.+)$;
                    fastcgi_pass unix:/var/run/php5-fpm.sock;
                    fastcgi_index index.php;
                    include fastcgi_params;
            }
            location ~ /\.ht {
                    deny all;
            }
            include /srv/www/example.com/block.conf;
    }



ToDo
=======================

Allow Quoting

Allow Image uploads
