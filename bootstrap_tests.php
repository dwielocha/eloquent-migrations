<?php
/**
 * Boostrap file for testing
 * 
 * @author Damian Wielocha <damian@wielocha.com>
 */
require_once 'vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$config = [
    'driver' => getenv('DB_CONNECTION'),
    'host' => getenv('DB_HOST'),
    'port' => getenv('DB_PORT'),
    'database' => getenv('DB_DATABASE'),
    'username' => getenv('DB_USERNAME'),
    'password' => getenv('DB_PASSWORD'),
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
];

$capsule = new Illuminate\Database\Capsule\Manager();
$capsule->addConnection($config);
$capsule->setAsGlobal();
$capsule->bootEloquent();
