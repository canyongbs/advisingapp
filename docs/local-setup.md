# Local Setup

### Requirements
* [Docker](https://docs.docker.com/get-docker/)
* [Docker Compose](https://docs.docker.com/compose/install/) (It is most likely that the way you installed Docker already came with Docker Compose, so on most systems you probably need not install this)
* [NVM (Node Version Manager)](https://github.com/nvm-sh/nvm) (Optional, but recommended)
* Spin CLI
  * [Spin MacOS](https://serversideup.net/open-source/spin/docs/installation/install-macos#install-docker-desktop)
  * [Spin Linux](https://serversideup.net/open-source/spin/docs/installation/install-linux)

### Pre-Setup

#### NVM

NVM is used in this project to ensure that the correct version of Node is being used. This is not a requirement, but it is highly recommended. If you do not wish to use NVM, you can simply install the correct version of Node on your host machine and skip the NVM setup.

To install NVM, follow the instructions on the [NVM GitHub page | Installing and Updating](https://github.com/nvm-sh/nvm#installing-and-updating)

After installing NVM, you can install the correct version of Node by running the following command in the root of the project:

```bash
nvm install
```

This will install the version of Node specified in the `.nvmrc` file. You can then use this version of Node by running the following command:

```bash
nvm use
```

Details on how to automatically use the correct version of Node when entering the project directory can be found on the [NVM GitHub page | Deeper Shell Integration](https://github.com/nvm-sh/nvm#deeper-shell-integration)

### Setup

#### 1. Set up the `.env` file
First, create an `.env` file based on `.env.example`
```bash
cp .env.example .env
```

---

#### 2. Install Composer Dependencies

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs --no-scripts
```

---

#### 3. Install NPM Dependencies

```bash
npm install --os linux
```

> **Note:** If you have properly installed NVM then nvm should automatically switch to the correct version of Node and NPM. If you have not installed NVM, then you will need to ensure that you are using the correct version of Node and NPM.

---

#### 4. Start the containers and open a shell into the main PHP container

Run the following command to start the containers:

```bash
spin up -d
```

Once the containers are started you can now start a shell into the main PHP container by running the following command:

```bash
spin exec -it advisingapp-app bash
```

All following commands will and should be run from within the PHP container.

---

#### 5. Set up the application

We will set up the application by running the following commands:
```bash
php artisan migrate:landlord:fresh
php artisan key:generate
php artisan queue:restart
php artisan schedule:interrupt
php artisan app:build-assets
```

The above commands will set up the application for the "landlord" database. The landlord database is in charge of holding all information on tenants. Next we will set up a tenant.

```bash
php artisan tenants:create [A Name for the Tenant] [A domain for the tenant]
```

These commands will create a new tenant with the name and domain you supplied and then refresh and seed the tenant's database.

After this the application should be accessible at the domain you supplied.

Spin can be stopped by running `spin stop` and turning back on by running `spin up -d`

### Customizing container settings and Ports

Within the `.env.example` (and within the `.env` after you copy it) should exist the following variables:

```dotenv
FORWARD_DB_PORT=3306
FORWARD_DB_PORT_TEST=3309
FORWARD_REDIS_PORT=6379
FORWARD_MEILISEARCH_PORT=7700
FORWARD_MAILPIT_PORT=1025
FORWARD_MAILPIT_DASHBOARD_PORT=8025
FORWARD_MINIO_PORT=9000
FORWARD_MINIO_CONSOLE_PORT=8900
```

Those variable will allow you to edit particular settings and forwarding ports for your local containers. A great example of this usage is within the database section below.

### Accessing the Database
Within the containers, MySQL lives on port 3306. And by default it can be accessed outside of the containers on port 3308 as well.

If port 3306 is already in use on your system or you prefer to use another port,
you can set the `FORWARD_DB_PORT` in your `.env` file to whatever available
port you want.

### Minio (S3 Compatible Storage)
Minio is a S3 compatible storage solution that is used for storing files locally.

When first setting up you will need to create a bucket. This can be done by going to `localhost:8900` in your browser and logging in with `advisingapp` as the username and `password` as the password. Once logged in, you can create a bucket.

By default, the application is set up in the `.env.example` to reference a bucket named `local`. Create a bucket with this name in Minio. Then change its access policy to "Custom" with the following policy configuration:

```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "AllowPublicRead",
            "Effect": "Allow",
            "Principal": {
                "AWS": [
                    "*"
                ]
            },
            "Action": [
                "s3:GetObject"
            ],
            "Resource": [
                "arn:aws:s3:::local/PUBLIC/*"
            ]
        }
    ]
}
```

In order to facilitate proper file upload with Livewire you will need to set the following in your local etc/hosts file:
```
127.0.0.1 minio
```

### Queue and Scheduler

The application should automatically start a queue worker and scheduler when you run `spin up -d`. If you preferred to not have these running. You can see the corresponding `env` variables to false like so:

```dotenv
LARAVEL_SCHEDULER_ENABLED=false
LARAVEL_QUEUE_ENABLED=false
```
