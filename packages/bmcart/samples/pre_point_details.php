<?php

ini_set("display_errors", On);
error_reporting(E_ALL);

require '../../../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager AS Capsule;
use Packages\Bmcart\Model\PrePointDetailsRepository;
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

$prePointDetailsRepository = new PrePointDetailsRepository();
Capsule::enableQueryLog();

if (isset($_POST['pre_point_details_submit'])) {
    $order_id = $_POST['order_id'];
    $user_id = $_POST['user_id'];
    $pre_point_id = $_POST['pre_point_id'];

    $order_details_id_data = $_POST['order_details_id_data'];
    $card_point_data = $_POST['card_point_data'];
    $order_cards_new_ids = [];
    foreach ($order_details_id_data as $order_details_id) {
        foreach ($card_point_data as $card_point) {
            $order_cards_new_ids[$order_details_id] = [
                'id' => $order_details_id,
                'card_point' => $card_point
            ];
        }
    }

    $prePointDetailsEntity = $prePointDetailsRepository->insertPrePointDetails($order_id, $user_id, $pre_point_id, $order_cards_new_ids);
    $logs = Capsule::getQueryLog();
    $log->addDebug(json_encode($logs, JSON_UNESCAPED_UNICODE));
}

$pre_point_details = $prePointDetailsRepository->getPrePointDetails(1);

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
        <input type="hidden" name="user_id" value="1" />
    </div>
    <div>
        <label for="order_id">order_id</label>
        <input id="order_id" type="text" name="order_id" required />
    </div>
    <div>
        <label for="pre_point_id">pre_point_id</label>
        <input id="pre_point_id" type="text" name="pre_point_id" required />
    </div>
    <div>
        order_details_id : 1111<br />
        card_point : 20<br />
        <input type="hidden" name="order_details_id_data[]" value="1111" />
        <input type="hidden" name="card_point_data[]" value="20" />
    </div>
    <div>
        order_details_id : 2222<br />
        card_point : 40<br />
        <input type="hidden" name="order_details_id_data[]" value="2222" />
        <input type="hidden" name="card_point_data[]" value="40" />
    </div>
    <div>
        <input type="submit" name="pre_point_details_submit" value="submit">
    </div>
</form>
<p>&nbsp;</p>
<table>
    <tr>
        <th>pre_point_detail_id</th>
        <th>order_new_id</th>
        <th>order_cards_new_id</th>
        <th>user_id</th>
        <th>legacy_user_id</th>
        <th>pre_point_id</th>
        <th>point</th>
        <th>created</th>
        <th>modified</th>
    </tr>
    <?php foreach ($pre_point_details as $pre_point_detail) { ?>
        <tr>
            <td><?= $pre_point_detail->pre_point_detail_id ?></td>
            <td><?= $pre_point_detail->order_new_id ?></td>
            <td><?= $pre_point_detail->order_cards_new_id ?></td>
            <td><?= $pre_point_detail->user_id ?></td>
            <td><?= $pre_point_detail->legacy_user_id ?></td>
            <td><?= $pre_point_detail->pre_point_id ?></td>
            <td><?= $pre_point_detail->point ?></td>
            <td><?= $pre_point_detail->created ?></td>
            <td><?= $pre_point_detail->modified ?></td>
        </tr>
    <?php } ?>
</table>
<?php
//var_dump($pre_points);
?>
</body>
</html>