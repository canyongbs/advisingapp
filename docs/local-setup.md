# Local Setup

### Requirements
* [Docker](https://docs.docker.com/get-docker/) version `26.0.0` or higher
* [Docker Compose](https://docs.docker.com/compose/install/) version `2.25.0` or higher (It is most likely that the way you installed Docker already came with Docker Compose, so on most systems you probably need not install this)
* [NVM (Node Version Manager)](https://github.com/nvm-sh/nvm) (Optional, but recommended)

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

#### Local hosts file

In order to access the application in a web browser either in local or remote development you will need to place the following in your local `/etc/hosts` (or equivalent) file:

```bash
127.0.0.1 advisingapp.local
127.0.0.1 mail.tools.advisingapp.local
127.0.0.1 redis.tools.advisingapp.local
127.0.0.1 test.advisingapp.local
```

> Note: If you want any other tenant domains other than `test.advisingapp.local` you will need to add them to your `etc/hosts` file in the same way as well.

### Setup

Going forward this document will make reference to the `pls` command.

The `pls` command is a custom made script that acts as a tool to run our sometimes complex `docker compose` and other related commands. It is located in the root directory of the project and can be run by using `./pls`.

It comes with an install command `./pls install` that will "install" the current version into your home directory `bin` folder so that it can be used like a regular command `pls`. This documentation will assume you have done so.

#### 1. Set up the `.env` file
First, create an `.env` file based on `.env.example`
```bash
cp .env.example .env
```

---

#### 2. Install Dependencies

##### Composer Dependencies

```bash
pls ih composer install --ignore-platform-reqs
```

##### JS Dependencies Installation and Build

```bash
pls ih npm ci && npm run build
```

---

#### 3. Start the containers and open a shell into the main PHP container

Generate an encryption key:

```bash
php artisan key:generate
```

Run the following command to start the containers:

```bash
pls up -d
```

Once the containers are started you can now start a shell into the main PHP container by running the following command:

```bash
pls shell
```

All following commands will and should be run from within the PHP container.

---

#### 4. Set up the application

Generate an App Key:

We will set up the application by running the following commands:
```bash
php artisan migrate:landlord:fresh
```

The above commands will set up the application for the "landlord" database. The landlord database is in charge of holding all information on tenants. Next we will set up a tenant.

> Note: This command above actually in most cases is not needed since the Landlord is migrated on initial startup of the web service in local development

```bash
php artisan tenants:create [A Name for the Tenant] [A domain for the tenant]
```

Example : `php artisan tenants:create test test.advisingapp.local`

These commands will create a new tenant with the name and domain you supplied and then refresh and seed the tenant's database.

After this the application should be accessible at the domain you supplied.

The containers can be stopped by running `pls stop` and turned back on by running `pls up -d`

Setup is now complete.

---

### Customizing container settings and Ports

Within the `.env.example` (and within the `.env` after you copy it) should exist the following variables:

```dotenv
FORWARD_DB_PORT=3306
FORWARD_DB_PORT_TEST=3309
FORWARD_REDIS_PORT=6379
FORWARD_MEILISEARCH_PORT=7700
FORWARD_MAILPIT_PORT=1025
FORWARD_MAILPIT_DASHBOARD_PORT=8025
```

Those variables will allow you to edit particular settings and forwarding ports for your local containers. A great example of this usage is within the database section below.

### Accessing the Database
Within the containers, MySQL lives on port 3306. And by default it can be accessed outside of the containers on port 3308 as well.

If port 3308 is already in use on your system or you prefer to use another port,
you can set the `FORWARD_DB_PORT` in your `.env` file to whatever available
port you want.

### Storage
This application makes use of S3 for storage. If you would like to use local storage. In order to do so, create a new public s3 bucket with the following policy:

```json
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Sid": "AllowPublicRead",
      "Effect": "Allow",
      "Principal": {
        "AWS": "*"
      },
      "Action": "s3:GetObject",
      "Resource": "arn:aws:s3:::[YOUR_S3_BUCKET_NAME]/PUBLIC/*"
    }
  ]
}
```

After creating the bucket, you can set the following variables in your `.env` file:

```dotenv
AWS_S3_ACCESS_KEY_ID=
AWS_S3_SECRET_ACCESS_KEY=
AWS_S3_DEFAULT_REGION=
AWS_S3_BUCKET=
AWS_S3_ROOT=
```

### Queue and Scheduler

`pls up -d` will automatically start the app, worker, and scheduler services.

As the names suggest, the worker and scheduler service are dedicated services for the running of a queue worker and an on-going schedule run process.

Having this working is useful in most use-cases. But if you want more control over when / how the queue or scheduler is run then you can specify which services you want to turn on in the `pls up` command.

For example, if you just wanted the app service you could run `pls up app -d`. Or if you wanted the app and the scheduler service you could run `pls up app scheduler -d`