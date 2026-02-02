cd ..
c:\xampp\my_php\php.exe -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
c:\xampp\my_php\php.exe -r "if (hash_file('sha384', 'composer-setup.php') === 'c8b085408188070d5f52bcfe4ecfbee5f727afa458b2573b8eaaf77b3419b0bf2768dc67c86944da1544f06fa544fd47') { echo 'Installer verified'.PHP_EOL; } else { echo 'Installer corrupt'.PHP_EOL; unlink('composer-setup.php'); exit(1); }"
c:\xampp\my_php\php.exe composer-setup.php
c:\xampp\my_php\php.exe -r "unlink('composer-setup.php');"

cd backend
c:\xampp\my_php\php.exe ../composer.phar require lcobucci/jwt
c:\xampp\my_php\php.exe ../composer.phar require lcobucci/clock

c:\xampp\mysql\bin\mysql.exe -u root -e "source ../db/main_db.sql"
c:\xampp\mysql\bin\mysql.exe -u root -e "use ordayna_main_db;source ../db/main_db_procedures.sql;source ../db/test_data.sql;"

echo "very secret key because windows sucks" > secret.key

cd ../config
