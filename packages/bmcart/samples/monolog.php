<?php
ini_set("display_errors", On);  
error_reporting(E_ALL);

require '../../../vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\ChromePHPHandler;

$log = new Logger('cool-php-libraries');
$log->pushHandler(new StreamHandler('../../../logs/app.log', Logger::DEBUG));
$log->pushHandler(new ChromePHPHandler(Logger::DEBUG));
$log->addInfo('トップページの表示');
$log->addError('エラーメッセージ');
$log->addDebug('デバッグ用のメッセージ');
