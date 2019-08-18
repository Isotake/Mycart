<?php
ini_set("display_errors", On);  
error_reporting(E_ALL);

require '../../../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::create('../../../');
$dotenv->load();

$version = getenv('VERSION');
echo $version;
