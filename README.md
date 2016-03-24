
# Dev Setup #

*NOTE:* These steps are incomplete!

After having cloned this-here repository, you'll need to pull down the
[php-spare-parts library](https://github.com/JustAHappyKid/php-spare-parts)
using the following two commands:

    git submodule init
    git submodule update

You'll need to have MySQL and the PHP/MySQL driver available (in addition to PHP). If
you're on a Debian-based system (e.g., Ubuntu) something like the following
command should do the trick:

    sudo apt-get install mysql-server php5-mysql

Create a test database and user to connect to that database...

     mysql -u root -p -e "
      CREATE DATABASE chipin_test;
      CREATE USER 'chipin_test'@'localhost' IDENTIFIED BY 'password';
      GRANT ALL PRIVILEGES ON chipin_test.* TO 'chipin_test'@'localhost';"

Bootstrap the database with the schema and mock ticker/price data:

    mysql -u root -p chipin_test < schema.sql
    mysql -u root -p chipin_test -e "
      insert into ticker_data values ('USD', 500);
      insert into ticker_data values ('CAD', 600);
      insert into ticker_data values ('CNY', 4000);"

