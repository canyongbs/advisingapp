# Local Setup

### Requirements
* [Docker](https://docs.docker.com/get-docker/)
* [Docker Compose](https://docs.docker.com/compose/install/) (It is most likely that the way you installed Docker already came with Docker Compose, so on most systems you probably need not install this)
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

### Setup
This application makes use of [Spin](https://serversideup.net/open-source/spin/docs) for local development. Though not a requirement, it is highly recommended reading through the documentation on it.

The `spin` executable is within your vendor folder, so you would have to type the path to it everytime to use it. To make this better, Sail recommends adding the following Bash alias:

```bash
alias spin='[ -f node_modules/.bin/spin ] && bash node_modules/.bin/spin || bash vendor/bin/spin'
```

This documentation will assume you have done so. If not you can simply replace `spin` throughout with `./vendor/bin/spin`.

It may also be helpful to add some aliases for quick artisan and composer commands.

```bash
alias spina='spin exec -it php php artisan'
alias spinc='spin exec -it php php composer'
```

Make sure to add these after the `spin` alias.

If you choose not to add these aliases, you can execute commands using `exec` like so:

```bash
spin exec -it php php artisan key:generate

# or

spin exec -it php php composer install
```

---

After cloning this project, execute the following commands to install php dependencies:

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
```
You can install the php dependencies by simple running `composer install` on your host machine which can be quicker. But it can be best to install these making sure that the correct PHP version is being used while doing so.

Then, create a `.env` file based on `.env.example`
```bash
cp .env.example .env
```

Next we need to get Sail to set up the containers and start running:

```bash
sail up -d
```

Finally, we will set up the application by running the following commands:
```bash
sail artisan key:generate
sail artisan migrate:landlord:fresh
sail npm install
sail npm run build
```

These commands will set up the application for the "landlord" database. The landlord database is in charge of holding all information on tenants. Next we will set up a tenant.

```bash
sail artisan tenant:create [A Name for the Tenant] [A domain for the tenant]
sail artisan queue:work --queue=landlord --stop-when-empty
sail artisan tenants:artisan "db:seed --database=tenant"
```

These commands will create a new tenant with the name and domain you supplied, seed some data into it's sis database, and then refresh and seed the tenant's database.

After this the application should be accessible at the domain you supplied.

Sail can be stopped by running `sail stop` and turning back on by running `sail up -d`

### Customizing Sail settings and Ports

Within the `.env.example` (and within the `.env` after you copy it) should exist the following variables:
```dotenv
# The following env variables are only needed for local Laravel Sail instances
FORWARD_DB_PORT=3306
FORWARD_DB_PORT_TEST=3309
FORWARD_REDIS_PORT=6379
FORWARD_MEILISEARCH_PORT=7700
FORWARD_MAILPIT_PORT=1025
FORWARD_MAILPIT_DASHBOARD_PORT=8025
SAIL_XDEBUG_MODE=off
SAIL_XDEBUG_CONFIG=client_host=host.docker.internal
FORWARD_MINIO_PORT=9000
FORWARD_MINIO_CONSOLE_PORT=8900
# End Laravel Sail env variables
```

Those variable will allow you to edit particular settings and forwarding ports for Sail. A great example of this usage is within the database section below.

### Accessing the Database
Within the containers, MySQL lives on port 3306. And by default it can be accessed outside of the containers on port 3308 as well.

If port 3306 is already in use on your system or you prefer to use another port,
you can set the `FORWARD_DB_PORT` in your `.env` file to whatever available
port you want.

### Seed Mass ADM Data
In order to seed the ADM data, you will need to first create a shell within sail by running the following command:
```bash
sail shell
```

Then you can run the following command within the sail container to seed the data:

```bash
source .env ; gunzip < resources/sql/advising-app-adm-data.gz | PGPASSWORD=$SIS_DB_PASSWORD psql -h $SIS_DB_HOST -p $SIS_DB_PORT -U $SIS_DB_USERNAME -d $SIS_DB_DATABASE -q
```

### Minio (S3 Compatible Storage)
Minio is a S3 compatible storage solution that is used for storing files locally.

When first setting up you will need to create a bucket. This can be done by going to `localhost:8900` in your browser and logging in with `sail` as the username and `password` as the password. Once logged in, you can create a bucket.

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


