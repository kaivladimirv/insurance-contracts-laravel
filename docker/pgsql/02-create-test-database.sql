SELECT 'CREATE DATABASE contracts_laravel_test'
WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = 'contracts_laravel_test')\gexec
