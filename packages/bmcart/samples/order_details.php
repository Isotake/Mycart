<?php

ini_set("display_errors", On);
error_reporting(E_ALL);

require '../../../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager AS Capsule;
use Packages\Bmcart\Model\OrderDetailsRepository;
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

$test_order_id = 1;

$orderDetailsRepository = new OrderDetailsRepository();

if (isset($_POST['order_cards_submit'])) {
    $insert_data = [
        [
            'order_id' => $_POST['order_id'],
            'item_id' => $_POST['item_id'],
            'user_id' => $_POST['user_id'],
            'price' => $_POST['price'],
            'number' => $_POST['number'],
            'point' => $_POST['point'],
        ]
    ];
    $orderDetailsEntity = $orderDetailsRepository->insertOrderDetails($insert_data);
}

$order_cards = $orderDetailsRepository->getOrderDetailsById($test_order_id);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>
<form action="" method="post">
    <label>order_id</label>: <input type="text" name="order_id" value="<?= $test_order_id ?>" /><br />
    <label>item_id</label>: <input type="text" name="item_id" value="2984096" /><br />
    <label>user_id</label>: <input type="text" name="user_id" value="129425" /><br />
    <label>price</label>: <input type="text" name="price" value="1000" /><br />
    <label>number</label>: <input type="text" name="number" value="1" /><br />
    <label>point</label>: <input type="text" name="point" value="10" /><br />
    <input type="submit" name="order_cards_submit" value="submit"><br />
<!--</form>-->
<p>&nbsp;</p>
<table>
    <tr>
        <th>id</th>
        <th>order_id</th>
        <th>item_id</th>
        <th>user_id</th>
        <th>price</th>
        <th>number</th>
        <th>point</th>
    </tr>
    <?php foreach ($order_cards as $order_card) { ?>
        <tr>
            <td><?= $order_card->id ?></td>
            <td><?= $order_card->order_id ?></td>
            <td><?= $order_card->item_id ?></td>
            <td><?= $order_card->user_id ?></td>
            <td><?= $order_card->price ?></td>
            <td><?= $order_card->number ?></td>
            <td><?= $order_card->point ?></td>
        </tr>
    <?php } ?>
</table>
<?php var_dump($order_cards); ?>
</body>
</html>