# phpMySQLapp
A sample LAMP based web service stack.
LAMP stack is a popular open source web platform commonly used to run dynamic web sites and servers. 
It includes Linux, Apache, MySQL, and PHP and is considered by many the platform of choice for development 
and deployment of high performance web applications which require a solid and reliable foundation.

![Alt text](https://github.com/Anirban2404/phpMySQLapp/blob/master/homePage.JPG "Screen Shot")

### Setup Databases
* We are using Mysql as the database, so you need to Install MySQL and Configure MySQL properly.
* Install git and clone the repository.
* The *.sql files are located in the mySqlDB folder.
* Create two databases and name it "bookstore" and "moviedb", set collation to "utf8_unicode_ci";
* "Import" the "mySqlDB/movieDB.sql" and "mySqlDB/bookDB.sql" files, it will create the tables and populate the tables with initial data.
You can use phpmyadmin to import or you can do it from the terminal
```
mysql -u <username> -p <databasename> < <filename.sql>
```

### Setup WebApplication
* You have to install Apache2 and PHP and configure it.
* Install git and clone the repository.
* In order for Apache to find the file and serve it correctly, it must be saved to a very specific directory, which is called the "web root". In Ubuntu 16.04, this directory is located at /var/www/html/ -- copy the git source code inside it. Folder Structure will be like below.
```
ubuntu@mywebserver:/var/www/html$ tree
.
├── admin_area
│   ├── insertbook.php
│   ├── insert_books.php
│   ├── insertmovie.php
│   └── insert_movies.php
├── books
│   ├── functions
│   │   ├── fetch.php
│   │   ├── functions.php
│   │   └── getbook.php
│   ├── home.php
│   ├── images
│   │   └── background_image.jpg
│   └── includes
│       └── bookDatabase.php
├── homePage.JPG
├── index.php
├── movies
│   ├── functions
│   │   ├── fetch.php
│   │   ├── functions.php
│   │   └── getmovie.php
│   ├── home.php
│   ├── images
│   │   └── background_image.jpg
│   └── includes
│       └── movieDatabase.php
├── mySqlDB
│   ├── bookDB.sql
│   └── movieDB.sql
├── README.md
└── siteImages
    ├── books.jpg
    └── movies.jpg
```   

#### Need to change db connection address at webserver node
Finally, you have to access the database from the webapplication.
You have to change the database access endoints in books/includes/bookDatabase.php and movies/includes/movieDatabase.php.
```
sudo sed -i -e 's/127.0.0.1/<<ip_address>>/g' /var/www/html/books/includes/bookDatabase.php 
```
```
sudo sed -i -e 's/127.0.0.1/<<ip_address>>/g' /var/www/html/movies/includes/movieDatabase.php
```
The default username is root and password is admin, if you change that make sure you also change it in books/includes/bookDatabase.php and movies/includes/movieDatabase.php.





### Instrumenting the app


Execute the following commands and check if there are any errors while executing them and troubleshoot them if needed.

# PhP installation
sudo apt -y update
sudo apt install -y lsb-release gnupg2 ca-certificates apt-transport-https software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt -y install php8.2-cli php8.2-fpm php8.2-common php8.2-mysql php8.2-pgsql php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl php8.2-xml php8.2-bcmath php8.2-memcached
php -v
# Mysql installation
sudo apt -y install mysql-server
mysql --version
# Install Composer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === 'e21205b207c3ff031906575712edab6f13eb0b361f2085f1f1237b7126d785e826a450292b6cfd1d64d92e6563bbde02') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
sudo mv composer.phar /usr/local/bin/composer
# Install apache
sudo apt -y update
sudo apt -y install apache2
sudo ufw app list
sudo ufw allow 'Apache'
sudo ufw status
sudo systemctl status apache2
# Instrumentation settings
composer require guzzlehttp/guzzle
composer require \
  open-telemetry/sdk \
  open-telemetry/exporter-otlp
apt install php-pear
sudo apt-get install php-dev
sudo apt-get install zlib1g-dev	
pecl install grpc


In phpMySQL folder run php -S 0.0.0.0:8097

In browser hit 0.0.0.0:8097/index.php and 0.0.0.0:8097/books/home.php to generate traces
