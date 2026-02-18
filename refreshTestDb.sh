php bin/console doctrine:database:drop --env=test --force --if-exists
php bin/console doctrine:database:create --env=test
php bin/console doctrine:migrations:migrate --env=test --no-interaction