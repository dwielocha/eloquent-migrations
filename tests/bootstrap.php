<?php
/**
 * Boostrap file for testing
 *
 * @author Damian Wielocha <damian@wielocha.com>
 */
require_once 'vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__.'/../');
$dotenv->load();

$config = [
    'driver' => env('DB_CONNECTION'),
    'host' => env('DB_HOST'),
    'port' => env('DB_PORT'),
    'database' => env('DB_DATABASE'),
    'username' => env('DB_USERNAME'),
    'password' => env('DB_PASSWORD'),
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
];

$capsule = new Illuminate\Database\Capsule\Manager();
$capsule->addConnection($config);
$capsule->setAsGlobal();
$capsule->bootEloquent();
