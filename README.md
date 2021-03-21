# Oasys

API coverage:

[![coverage report](https://github.com/richhaho/oasys/badges/develop/coverage.svg)](https://github.com/richhaho/oasys/commits/develop)

## Setup

### Prerequisites

- Install the latest version of [Docker](https://docs.docker.com/install/) and [Docker Compose](https://docs.docker.com/compose/install/).
On MacOS and Windows, both are bundled inside the Docker Desktop application.
- **Using a Linux operating system like Ubuntu is strongly recommended.**
- On Windows, make sure to use a *bash-like* terminal.
- Clone the project using `git clone https://github.com/richhaho/oasys` inside
your working directory.

### First steps

On MacOS and Windows, update your `hosts` file with:

```
127.0.0.1   oasys.localhost
127.0.0.1   api.oasys.localhost
127.0.0.1   phpmyadmin.oasys.localhost
127.0.0.1   mailcatcher.osasys.localhost
```

Then copy the file `.env.template` to a file named `.env`. For instance:

```bash
cp .env.template .env
```

**Next, make sure that there is no application running on port 80**, then start all the Docker containers with the following commands:

```bash
make up
```

It may take some time as each containers will also install dependencies (PHP, JavaScript etc.), 
compile raw sources (JavaScript) or run migrations for setting up the databases structures.

Next time you run this command, the containers should be ready faster as most of the setting up will already be done.

### API

Every command stated here has to be run inside the container:

```
make api
// do your stuff.
```

#### Preparing your MR on GitLab

Before each push, don't forget to run the static analyzers with:

```
composer csfix
composer analyze
```

Also run PHPUnit tests with:

```
php bin/phpunit
```

**Note:** this command will generate a coverage in text (stdout) and in HTML (`src/api/coverage`).

#### Working with the database

First, create a `patch`:

```bash 
php bin/console doctrine:migrations:generate
```

Once your `patch` is done, execute it:

```bash
php bin/console doctrine:migrations:migrate
```

Finally regenerate the `Daos` and `Model` with:

```bash
php bin/console tdbm:generate
```

### Resetting the database

```bash
php bin/console doctrine:database:drop -n --force
php bin/console doctrine:database:create -n
php bin/console doctrine:migrations:migrate -n
php bin/console users:create-super-admin
```

#### Testing GraphQL endpoints

Go to http://api.oasys.localhost/graphiql.
