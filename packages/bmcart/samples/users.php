<?php

ini_set("display_errors", On);
error_reporting(E_ALL);

require '../../../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager AS Capsule;
use Packages\Bmcart\Model\UsersRepository;
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

$usersRepository = new UsersRepository();
$user = $usersRepository->findById(1);

echo $user->user_id . '<br />';
echo $user->username . '<br />';
echo $user->email . '<br />';
echo $user->group_id . '<br />';
echo $user->first_name . '<br />';
echo $user->first_name_mb . '<br />';
echo $user->last_name . '<br />';
echo $user->last_name_mb . '<br />';
echo $user->phone . '<br />';
echo $user->postcode . '<br />';
echo $user->prefecture . '<br />';
echo $user->city . '<br />';
echo $user->town . '<br />';
echo $user->address . '<br />';
echo $user->apartment . '<br />';

var_dump($user);