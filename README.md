# README

Example of bootstrapped project with `api-platform` and `symfony 3`.

## Installation

### Development environment

```sh
cp .env.dist .env
sed --in-place 's@POSTGRES_HOST=.*@POSTGRES_HOST=postgresql@g' .env
sed --in-place 's@REDIS_HOST=.*@REDIS_HOST=redis@g' .env

cat > docker-compose.override.yml << EOF
version: '2.3'

services:

    adminer:
        image: adminer:standalone
        init: true
        ports:
            - '8080:8080'
        read_only: true
        tmpfs:
            - /tmp

    nginx:
        ports:
            - '80:80'
        volumes:
            - './:/srv'

    php-fpm:
        environment:
            - 'GROUP=$(id -g)'
            - 'USER=$(id -u)'
        volumes:
            - '${HOME}/.composer:/home/www-data/.composer'
            - './:/srv'
EOF

docker-compose up -d
docker-compose exec -u www-data php-fpm bin/console doctrine:schema:update --force
# Go to "http://localhost/"
```

### Production environment

```sh
cp .env.dist .env
sed --in-place 's@POSTGRES_HOST=.*@POSTGRES_HOST=postgresql@g' .env
sed --in-place 's@POSTGRES_PASSWORD=.*@POSTGRES_PASSWORD=MySuperPassword@g' .env
sed --in-place 's@REDIS_HOST=.*@REDIS_HOST=redis@g' .env
sed --in-place 's@SECRET=.*@SECRET=MySuperToken@g' .env
sed --in-place 's@SYMFONY_ENV=.*@SYMFONY_ENV=prod@g' .env
# Edit ".env" with your information

cat > docker-compose.override.yml << EOF
version: '2.3'

services:

    nginx:
        ports:
            - '80:80'
        volumes:
            - php-assets:/srv/web/bundles

    php-fpm:
        volumes:
            - php-assets:/srv/web/bundles

volumes:

    php-assets: ~
EOF

docker-compose up -d
docker-compose exec -u www-data php-fpm bin/console doctrine:schema:update --force
# Go to "http://localhost/"
```

## Usage

### Administration

This example can be used with [the dedicated administration](https://github.com/mauchede/example-api-platform-admin) (see branches `*/bootstrap`)`.

### Run unit tests

```sh
docker-compose exec -u www-data php-fpm vendor/bin/simple-phpunit
```

## Contributing

1. Fork it.
2. Create your branch: `git checkout -b my-new-feature`.
3. Commit your changes: `git commit -am 'Add some feature'`.
4. Push to the branch: `git push origin my-new-feature`.
5. Submit a pull request.

## Links

* [api-platform/api-platform](https://github.com/api-platform/api-platform)
* [docker-compose](https://docs.docker.com/compose/)
* [hautelook/alice-bundle](https://github.com/hautelook/AliceBundle)
* [timonier/mailcatcher](https://github.com/timonier/mailcatcher)
* [timonier/php](https://github.com/timonier/php)
* [timonier/postgresql](https://github.com/timonier/postgresql)
* [timonier/redis](https://github.com/timonier/postgresql)
