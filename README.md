## Requirements
Composer
MySQL
PHP >= 8.1
## Required packages

composer require jms/serializer-bundle
composer require friendsofsymfony/rest-bundle
composer require symfony/maker-bundle     
composer require symfony/orm-pack

## Update env file 
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"

## Migration
php bin/console make:migration

 
## All fruits a saved into the DB send
```
php bin/console fruits:fetch
```

## Done
