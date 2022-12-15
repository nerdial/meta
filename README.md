symfony server:start --no-tls

bin/console hautelook:fixtures:load


php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create

php bin/console make:migration

php bin/console doctrine:migrations:migrate

php bin/console --env=test doctrine:database:drop --force
php bin/console --env=test doctrine:migrations:migrate


