# README

Example of upload with `api-platform` and `symfony 4`.

## Installation

### Development environment

```sh
cp .env.dist .env
sed --in-place 's@POSTGRES_HOST=.*@POSTGRES_HOST=postgresql@g' .env
sed --in-place 's@REDIS_HOST=.*@REDIS_HOST=redis@g' .env

cat > docker-compose.override.yaml << EOF
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
sed --in-place 's@APP_ENV=.*@APP_ENV=prod@g' .env
sed --in-place 's@APP_SECRET=.*@APP_SECRET=MySuperSecret@g' .env
sed --in-place 's@POSTGRES_HOST=.*@POSTGRES_HOST=postgresql@g' .env
sed --in-place 's@POSTGRES_PASSWORD=.*@POSTGRES_PASSWORD=MySuperPassword@g' .env
sed --in-place 's@REDIS_HOST=.*@REDIS_HOST=redis@g' .env
# Edit ".env" with your information

cat > docker-compose.override.yaml << EOF
version: '2.3'

services:

    nginx:
        ports:
            - '80:80'
        volumes:
            - php-assets:/srv/public/bundles

    php-fpm:
        volumes:
            - php-assets:/srv/public/bundles
            - php-files:/srv/var/files

volumes:

    php-assets: ~

    php-files: ~
EOF

docker-compose up -d
docker-compose exec -u www-data php-fpm bin/console doctrine:schema:update --force
# Go to "http://localhost/"
```

## Usage

### Administration

This example can be used with [the dedicated administration](https://github.com/mauchede/example-api-platform-admin) (see branches `*/upload`)`.

### Run unit tests

```sh
docker-compose exec -u www-data php-fpm vendor/bin/simple-phpunit
```

## How does it work?

There are two resources:
* Gallery (representation of [ImageGallery](http://schema.org/ImageGallery))
* Image (representation of [ImageObject](http://schema.org/ImageGallery))

Default API operations on `Image` have been customized:
* all collection operation have been disabled.
* all item operation (except `GET`) have been disabled.

To create an `Image`, a multipart form has to be send to `/images`. The new `Image` will be returned if request have been validated.

Resource `Image` contains:
* contentUrl (representation of [contentUrl](http://schema.org/contentUrl)).
* format (representation of [fileFormat](http://schema.org/fileFormat)).
* size (representation of [contentSize](http://schema.org/contentSize)).

__Note 1__: To retrieve the "original" file, follow the URL in `contentUrl`.

__Note 2__: This example can be used with [a dedicated administration](https://github.com/mauchede/example-api-platform-admin/tree/example/upload)

Upload workflow:
1. In `CreateImageAction::__invoke`, the HTTP file will be checked. If it is valid, a "temporary" file will be created.
2. An `Image` entity will be created with the temporary file. This entity will be persisted. After the insertion in database, the temporary file will be moved in the "final" folder and renamed with the ID of the entity (cf `MediaListener::postPersist`).
3. When an `Image` entity is extracted from the database, properties `contentUrl` and `file` will be injected (cf `MediaListener::postLoad`).
4. When an `Image` entity is removed, "original" file will be removed from the file system.

## Contributing

1. Fork it.
2. Create your branch: `git checkout -b my-new-feature`.
3. Commit your changes: `git commit -am 'Add some feature'`.
4. Push to the branch: `git push origin my-new-feature`.
5. Submit a pull request.

## Links

* [1up-lab/OneupFlysystemBundle](https://github.com/1up-lab/OneupFlysystemBundle)
* [api-platform/api-platform](https://github.com/api-platform/api-platform)
* [docker-compose](https://docs.docker.com/compose/)
* [doctrine events](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html)
* [mauchede/example-api-platform-admin](https://github.com/mauchede/example-api-platform-admin)
* [timonier/php](https://github.com/timonier/php)
* [timonier/postgresql](https://github.com/timonier/postgresql)
* [timonier/redis](https://github.com/timonier/postgresql)
* [thephpleague/flysystem](https://github.com/thephpleague/flysystem)
