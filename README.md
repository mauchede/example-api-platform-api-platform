# README

Example of bootstrapped project with `api-platform` and `symfony 4`.

## Installation

### Development environment

```sh
cp .env.dist .env
sed --in-place 's@DATABASE_HOST=.*@DATABASE_HOST=postgresql@g' .env

cat > docker-compose.override.yaml << EOF
version: '2.3'

services:

    nginx:
        ports:
            - '80:80'
        volumes:
            - './:/srv'

    php:
        environment:
            - 'GROUP=$(id -g)'
            - 'USER=$(id -u)'
        volumes:
            - './:/srv'
EOF

docker-compose up -d
docker-compose exec -u www-data php bin/console doctrine:schema:update --force
# Go to "http://localhost/"
```

### Production environment

```sh
cp .env.dist .env
sed --in-place 's@APP_ENV=.*@APP_ENV=prod@g' .env
sed --in-place 's@APP_SECRET=.*@APP_SECRET=MySuperSecret@g' .env
sed --in-place 's@DATABASE_HOST=.*@DATABASE_HOST=postgresql@g' .env
sed --in-place 's@DATABASE_PASSWORD=.*@DATABASE_PASSWORD=MySuperPassword@g' .env
sed --in-place 's@POSTGRES_PASSWORD=.*@POSTGRES_PASSWORD=MySuperPassword@g' .env
# Edit ".env" with your informations

cat > docker-compose.override.yaml << EOF
version: '2.3'

services:

    nginx:
        ports:
            - '80:80'
        volumes:
            - php-assets:/srv/public/bundles

    php:
        volumes:
            - php-assets:/srv/public/bundles

volumes:

    php-assets: ~
EOF

docker-compose up -d
docker-compose exec -u www-data php bin/console doctrine:schema:update --force
# Go to "http://localhost/"
```

## Usage

### Run unit tests

```sh
docker-compose exec -u www-data php bin/simple-phpunit
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
* [timonier/php](https://github.com/timonier/php)
* [timonier/postgresql](https://github.com/timonier/postgresql)
