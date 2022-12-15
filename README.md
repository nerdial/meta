symfony server:start --no-tls

bin/console hautelook:fixtures:load
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console make:migration
php bin/console doctrine:migrations:migrate


# Test

php bin/console --env=test make:migration
php bin/console --env=test doctrine:database:create
php bin/console --env=test doctrine:database:drop --force
php bin/console --env=test doctrine:migrations:migrate
bin/console hautelook:fixtures:load --env=test