## example-doctrine-reverse-engineering
Example for reverse engineering in [doctrine orm](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/)

Serves as basis for my `FFA/mfbf-orm-core` repository

Every schema update, I need to update the files here using the `composer doctrine-update` command (check usage below)

For another example, check [doctrine2-orm-tutorial](https://github.com/doctrine/doctrine2-orm-tutorial/)

## Usage

1. `composer require shadiakiki1986/example-doctrine-reverse-engineering`

## To update
0. Set up
  - `git clone ... && cd ...`
  - download [composer](https://getcomposer.org/download/)

1. install: `composer install`
  - to test: `vendor/bin/doctrine list`

2. set db connection info in `connection.yml` (check [connection.yml.dist](connection.yml.dist))

3. Ensure the proper driver installations for the connection info above
  - e.g. if `pdo_mysql`, will need `sudo pecl install pdo_mysql`
  - check `Install drivers` section below

4. Export the current database's schema
  - mysql: `mysqldump -h my.ip.com -u user --password=pass --no-data dbname > initdb/1-schema.sql`

5. Edit the `2-fix-schema.sql` as needed to fix the schema (e.g. missing primary key, etc)

6. Create a copy of the database using the just-created schema
  - mysql/mariadb + docker:
    - [mariadb dockerfile](http://hub.docker.com/_/mariadb)
    - `docker run --name some-mariadb -e MYSQL_ROOT_PASSWORD=my-secret-pw -e MYSQL_DATABASE=dbname -v $PWD/initdb:/docker-entrypoint-initdb.d mariadb`
  - mysql/mariadb on EC2 bare metal ubuntu:
    - `sudo apt-get install mariadb-server`
    - `sudo /etc/init.d/mysql start # stop when done`
    - create user
```
CREATE USER ubuntu;
SET PASSWORD FOR ubuntu = PASSWORD('newpass');
GRANT ALL PRIVILEGES on *.* to 'ubuntu'@'localhost' IDENTIFIED BY 'newpass';
GRANT SELECT on *.* to 'ubuntu'@'%' IDENTIFIED BY 'newpass';
```

    - expose port 3306 in AWS EC2 console security group
    - create db 

```
echo "create database dbname;"|mysql -u ubuntu
mysql -u ubuntu dbname < initdb/schema.sql
```

7. generate yml files and entities: `composer doctrine-update`
  - Note 1: the first time I used this, I did not include the `--update-entities` flag
  - Note 2: in case of errors,
    or in case the generated files do not include a foreign key for example
    (because the original database schema did not set foreign keys),
    add `ALTER` sql commands in the `fix-schema.sql` of the previous step and repeat


## Install drivers
Pre-reqs
```
sudo apt-get install php-pear php-dev unixodbc-dev
```

Install `pdo_mysql`: `sudo apt-get install php-mysql`

For dev, install `pdo_sqlite`: `sudo apt-get install php-sqlite3`

Install `sqlsrv pdo_sqlsrv`
```
sudo pecl update-channels
sudo pecl install pdo_sqlsrv sqlsrv
cd /etc/php/7.0/mods-available/
echo "extension=pdo_sqlsrv.so" |sudo tee pdo_sqlsrv.ini
echo "extension=sqlsrv.so" | sudo tee sqlsrv.ini
cd ../cli/conf.d
sudo ln -s ../../mods-available/pdo_sqlsrv.ini 
sudo ln -s ../../mods-available/sqlsrv.ini 
php -i|grep pdo_sqlsrv -i
php -i|grep sqlsrv -i
```

Install ms sql drivers
```
http://go.microsoft.com/fwlink/?LinkId=163712

sudo su
curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add -
curl https://packages.microsoft.com/config/ubuntu/16.04/prod.list > /etc/apt/sources.list.d/mssql-release.list
apt-get update
ACCEPT_EULA=Y apt-get install msodbcsql mssql-tools
# WONT WORK ... apt-get install unixodbc-dev-utf16
exit
```

Install extensions
```
sudo apt-get install php7.0-xml php-mbstring
```

