Icebreath2
=========

Icebreath2 is a PHP based data API used to retrive and proccess data from
online radio station broadcasting systems such as SHOUTcast and Icecast.

Icebreath was designed and put together by Liam Haworth (liam.haworth@hivemedia.net.au) for
[The Hive Radio][1] so that they could get information from their Icecast
server and display it on their website. It soon grew from there and now
supports SHOUTcast and ShoutIRC with more to come!

> The overriding design goal for Icebreath is to allow for a
> easy to use data api that can be quickly installed and setup
> without any hassle to the end user.

Version
----

`2.0_BETA`

**Because this software is still in beta some features and controllers may not
work how they should!**

Installation
--------------

### Apache

```console
$ git clone https://github.com/TheAuzzieBrony/Icebreath2.git icebreath2
$ cp -r ./icebreath2/* /var/www/html/
$ cd /var/www/html/
$ mv htaccess.basedir .htaccess
```

##### Apache - In Sub Directory

```console
$ git clone https://github.com/TheAuzzieBrony/Icebreath2.git icebreath2
$ mkdir -p /var/www/html/icebreath
$ cp -r ./icebreath2/* /var/www/html/icebreath/
$ cd /var/www/html/icebreath/
$ mv htaccess.subdir .htaccess
$ vim .htaccess
```

Once you have the file editor open, change the lines that say [SUBDIR] to the
name of the sub directory you have put icebreath2 in.

### NGINX

```console
$ git clone https://github.com/TheAuzzieBrony/Icebreath2.git icebreath2
$ cp -r ./icebreath2/* /var/www/html/
$ vim /etc/nginx/sites-enabled/default
```

Once you have the file editor open, add the following inside your *server* block

```nginx
    location / {
		try_files $uri $uri/ /index.php?$args;
	}

	location ~ \.php$ {
		try_files $uri =404;
		include fastcgi_params;
		fastcgi_pass php;
	}
```

##### NGINX - In Sub Directory

```console
$ git clone https://github.com/TheAuzzieBrony/Icebreath2.git icebreath2
$ mkdir -p /var/www/html/icebreath
$ cp -r ./icebreath2/* /var/www/html/icebreath
$ vim /etc/nginx/sites-enabled/default
```

Once you have the file editor open, add the following inside your *server* block

```nginx
    location /icebreath {
		try_files $uri $uri/ /icebreath/index.php?$args;
	}

	location ~ \.php$ {
		try_files $uri =404;
		include fastcgi_params;
		fastcgi_pass php;
	}
```

Configuration
----

Configuration of Icebreath2 is fairly simple, just open `config.example.php` in
your favourite file editor and follow the instructions that are inside. Once
you have changed all that you need to change, apply the configuration as so:

```console
$ mv config.example.php config.php
```

License
----

Copyright (c) Liam 'Auzzie' Haworth <liam.hawoth@hivemedia.net.au>, 2013-2014.

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
of the Software, and to permit persons to whom the Software is furnished to do
so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

[1]:https://hiveradio.net
