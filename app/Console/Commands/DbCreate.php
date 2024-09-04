<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PDO;
use PDOException;

class DbCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:create {--test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new database';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $shouldCreateTestDatabase = $this->option('test');
        $configConnection = $this->getConfigConnection($shouldCreateTestDatabase);

        $dbHost = $configConnection['host'];
        $dbPort = $configConnection['port'];
        $dbUser = $configConnection['username'];
        $dbPassword = $configConnection['password'];
        $dbName = $configConnection['database'];

        try {
            $pdo = new PDO("pgsql:host=$dbHost;port=$dbPort;dbname=postgres", $dbUser, $dbPassword);

            $select = $pdo->prepare('SELECT 1 FROM pg_database WHERE datname = :dbName;');
            $select->execute(['dbName' => $dbName]);

            if ($select->rowCount() === 0) {
                $pdo->exec("CREATE DATABASE $dbName;");

                $this->info("Database '$dbName' created successfully.");
            } else {
                $this->info("Database '$dbName' already exists.");
            }
        } catch (PDOException $exception) {
            $this->error('Failed to create database. Error: ' . $exception->getMessage());
        }
    }

    private function getConfigConnection(bool $forTestDatabase): array
    {
        return $forTestDatabase ? config('database.connections.pgsql_testing') : config('database.connections.pgsql');
    }
}
