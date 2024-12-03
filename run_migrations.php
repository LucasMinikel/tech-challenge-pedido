<?php

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host = $_ENV['DB_HOST'];
$port = $_ENV['DB_PORT'];
$dbname = $_ENV['DB_DATABASE'];
$username = $_ENV['DB_USERNAME'];
$password = $_ENV['DB_PASSWORD'];

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $migrations = glob(__DIR__ . '/migrations/*.sql');
    sort($migrations);

    foreach ($migrations as $migration) {
        $sql = file_get_contents($migration);
        $pdo->exec($sql);
        echo "Executed migration: " . basename($migration) . PHP_EOL;
    }

    echo "All migrations completed successfully." . PHP_EOL;
} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}
