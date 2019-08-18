<?php

ini_set("display_errors", On);
error_reporting(E_ALL);

require '../../../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager AS Capsule;
use Packages\Bmcart\Model\PrePointsRepository;
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

$prePointsRepository = new PrePointsRepository();
Capsule::enableQueryLog();

if (isset($_POST['pre_point_submit'])) {

    $order_id = $_POST['order_id'];
    $user_id = $_POST['user_id'];
    $operator_id = $_POST['operator_id'];
    $point_get = $_POST['point_get'];
    $use_point = $_POST['use_point'];

    $prePointsEntity = $prePointsRepository->insertPrePoints($order_id, $user_id, $operator_id, $point_get, $use_point);
    $logs = Capsule::getQueryLog();
    $log->addDebug(json_encode($logs, JSON_UNESCAPED_UNICODE));
//    header('Location: ./pre_points.php');
//    exit;
}

$pre_points = $prePointsRepository->getPrePoints(1)->groupBy('order_id');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>
<form action="" method="post">
    <div>
        user_id : 1<br />
        operator_id : 1<br />
        <input type="hidden" name="user_id" value="1" />
        <input type="hidden" name="operator_id" value="1" />
    </div>
    <div>
        <label for="order_id">order_id</label>
        <input id="order_id" type="text" name="order_id" required />
    </div>
    <div>
        <label for="point_get">獲得ポイント</label>
        <input id="point_get" type="text" name="point_get" value="0" required />
    </div>
    <div>
        <label for="use_point">使用ポイント</label>
        <input id="use_point" type="text" name="use_point" value="0" required />
    </div>
    <div>
        <input type="submit" name="pre_point_submit" value="submit">
    </div>
</form>
<p>&nbsp;</p>
<table>
    <tr>
        <th>pre_point_id</th>
        <th>order_id</th>
        <th>user_id</th>
        <th>operator_id</th>
        <th>point</th>
        <th>is_valid</th>
        <th>comment</th>
        <th>created_at</th>
        <th>modified_at</th>
        <th>name_consumes</th>
    </tr>
    <?php
    foreach ($pre_points as $order) {
        foreach ($order as $order_detail) {
    ?>
        <tr>
            <td><?= $order_detail->pre_point_id ?></td>
            <td><?= $order_detail->order_id ?></td>
            <td><?= $order_detail->user_id ?></td>
            <td><?= $order_detail->operator_id ?></td>
            <td><?= $order_detail->point ?></td>
            <td><?= $order_detail->is_valid ?></td>
            <td><?= $order_detail->comment ?></td>
            <td><?= $order_detail->created_at ?></td>
            <td><?= $order_detail->modified_at ?></td>
            <td><?= $order_detail->name_consumes ?></td>
        </tr>
    <?php
        }
    }
    ?>
</table>
<?php
//var_dump($pre_points);
?>
</body>
</html>