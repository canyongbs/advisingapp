#!/bin/bash

composer clear-sis-database

source ./.env

gunzip < ./resources/sql/assist-adm-data.gz | PGPASSWORD=$SIS_DB_PASSWORD psql -h $SIS_DB_HOST -p $SIS_DB_PORT -U $SIS_DB_USERNAME -d $SIS_DB_DATABASE -q