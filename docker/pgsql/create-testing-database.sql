SELECT 'CREATE DATABASE testing_landlord'
WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = 'testing_landlord')\gexec

SELECT 'CREATE DATABASE testing_tenant'
WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = 'testing_tenant')\gexec