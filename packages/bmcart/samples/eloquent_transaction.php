<?php

ini_set("display_errors", On);
error_reporting(E_ALL);

require '../../../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager AS Capsule;
use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\ChromePHPHandler;

$dotenv = Dotenv::create('../../../');
$dotenv->load();

$dbname = getenv('DBNAME');
$dbuser = getenv('DBUSER');
$dbpass = getenv('DBPASS');

$log = new Logger('bm-cart');
$log->pushHandler(new StreamHandler('../../../logs/sql.log', Logger::DEBUG));
$log->pushHandler(new ChromePHPHandler(Logger::DEBUG));

$db = new Capsule;
$db->addConnection([
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => $dbname,
    'username'  => $dbuser,
    'password'  => $dbpass,
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

$db->setAsGlobal();
$db->bootEloquent();

Capsule::enableQueryLog();

$db_connection = $db->getConnection();
$db_connection->beginTransaction();
$rows = $db::select('show databases');
$db_connection->commit();

$logs = Capsule::getQueryLog();
$log->addDebug(json_encode($logs, JSON_UNESCAPED_UNICODE));

foreach($rows as $row) {
    echo $row->Database . PHP_EOL;
}