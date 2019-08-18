<?php

ini_set("display_errors", On);
error_reporting(E_ALL);

require '../../../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager AS Capsule;
use Packages\Bmcart\Model\ItemsRepository;
use Dotenv\Dotenv;

$dotenv = Dotenv::create('../../../');
$dotenv->load();

$dbname = getenv('DBNAME');
$dbuser = getenv('DBUSER');
$dbpass = getenv('DBPASS');

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

$itemsRepository = new ItemsRepository();
$items = $itemsRepository->findById([1]);

foreach ($items as $item) {
    echo $item->item_id . '<br />';
    echo $item->name . '<br />';
    echo $item->maker . '<br />';
    echo $item->prefecture . '<br />';
    echo $item->comment . '<br />';
    echo $item->filename . '<br />';
    echo $item->is_reserve . '<br />';
    echo $item->mail_allowed . '<br />';
    echo $item->stock . '<br />';
    echo $item->reserve_stock . '<br />';
    echo $item->price . '<br />';
    echo $item->item_point . '<br />';
}

var_dump($item);