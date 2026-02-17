#!/bin/bash
set -euo pipefail

sudo apt-get --update full-upgrade -y
sudo apt-get install mariadb-server nginx php-fpm php-mysql 7zip composer -y

sudo mariadb -u root -e "source ../db/main_db.sql;use ordayna_main_db;source ../db/main_db_procedures.sql;"

rm -f ../dhparam
curl https://ssl-config.mozilla.org/ffdhe2048.txt > ../dhparam

sudo rm -rf /etc/nginx/
sudo mkdir /etc/nginx
sudo cp -r ./nginx_config/* /etc/nginx/

# Generate the random secret used for jwt hashing
openssl rand -out ../web_server/secret.key -base64 32
# Generate the certificate and secret used for https
rm -f ../ordayna.pem
rm -f ../ordayna.key
openssl req -x509 -nodes -days 730 -newkey rsa:2048 -keyout ../ordayna.key -out ../ordayna.pem -config san.cnf

sudo systemctl restart nginx php8.4-fpm

cd ../web_server
composer require lcobucci/jwt lcobucci/clock ramsey/uuid
echo "TODO: generate a config.php instead of doing this"
printf "localhost" > "database_address"
cd ../config

# This is intentionally not recursive
rm -f ../web_server/error_logs.txt
touch ../web_server/error_logs.txt
chmod a+wr ../web_server/error_logs.txt

rm -rf ../web_server/user_data
mkdir ../web_server/user_data
chmod -R a+wr ../web_server/user_data

printf "Make sure that the /etc/mysql/mariadb.cnf file includes the following lines:\n[mysqld]\nevent_scheduler = on\n"
printf "If you have updated this file than run the following command: sudo systemctl restart mariadb\n"
