# OpenSearch Search Indexing

For search indexing in this application we make use of OpenSearch. OpenSearch is a fork of Elasticsearch, and is a powerful search engine that can be used to index and search through large amounts of data.

## Before You Begin

Ensure the proper env variables are set in `.env`. You can find the variables you need in the `.env.example` file.

```
SCOUT_DRIVER=opensearch
SCOUT_PREFIX=local_
OPENSEARCH_HOST=opensearch
OPENSEARCH_USERNAME=admin
OPENSEARCH_PASSWORD=admin
OPENSEARCH_RETRYS=2
OPENSEARCH_REGION=us-west-2
OPENSEARCH_SERVICE=aoss
OPENSEARCH_IAM_KEY=null
OPENSEARCH_IAM_SECRET=null
```

You must ensure `SCOUT_DRIVER` is set to `opensearch` in order to use OpenSearch as the search driver.

It is also recommended that you prefix your index with something unique to your application. This is done by setting the `SCOUT_PREFIX` variable. In the example above, the prefix is set to `local_`. This means that the index will be named `local_applications` and `local_users`. Using your name, for example `kevin_local_` is sufficient.

The credentials and host of your OpenSearch instance should be set in the remaining fields. You must generate / retrieve those values yourself.

## Indices

OpenSearch indices are created by our open search migration files. For example the [Prospect Index Migration file](../../app-modules/prospect/opensearch/migrations/2023_10_12_164523_create_prospects_index.php)

These migrations are tracked separately from database schema migrations. And can be run with the following commands:

```bash
# Will migrate all indices normally
php artisan opensearch:migrate

# There are also fresh and refresh alternatives
php artisan opensearch:migrate:fresh
php artisan opensearch:migrate:refresh
```

We also have some of our own commands to help with managing indices:

```bash
# Will list all indices
php artisan opensearch:list-indices

# Will delete all indices
php artisan opensearch:clear-indices
```

## Documents & Searching

Examples of how to set up a Model to be synced with OpenSearch can be found in the [Prospect Model](../../app-modules/prospect/src/Models/Prospect.php)

And how to set up searching with it in the (ListProspects)[app-modules/prospect/src/Filament/Resources/ProspectResource/Pages/ListProspects.php] page

## Other Notes

It is recommended that you migrate and setup your indices before running your seeders, to ensure that the data is properly indexed.

More details on Opensearch can be found in its [documentation](https://opensearch.org/docs/latest/)