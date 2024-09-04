SELECT 'CREATE DATABASE contracts_laravel'
WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = 'contracts_laravel')\gexec
