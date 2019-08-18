<?php

ini_set("display_errors", On);
error_reporting(E_ALL);

require '../../../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager AS Capsule;
use Packages\Bmcart\Model\OrdersRepository;
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

$ordersRepository = new OrdersRepository();

if (isset($_POST['order_submit'])) {
    $insert_data = [
        'state' => $_POST['state'],
        'shipping_fee' => $_POST['shipping_fee'],
        'shipping' => $_POST['shipping'],
        'arriving_date' => ($_POST['arriving_date']) ? $_POST['arriving_date'] : null ,
        'arriving_time' => $_POST['arriving_time'],
        'payment' => $_POST['payment'],
        'payment_result' => $_POST['payment_result'],
        'user_id' => $_POST['user_id'],
        'has_reserve' => $_POST['has_reserve'],
        'comment' => $_POST['comment'],
        'shop_comment' => $_POST['shop_comment'],
        'created_at' => ($_POST['created_at']) ? $_POST['created_at'] : null ,
        'modified_at' => ($_POST['modified_at']) ? $_POST['modified_at'] : null ,
        'deleted_at' => ($_POST['deleted_at']) ? $_POST['deleted_at'] : null ,
    ];
    $orderNewEntity = $ordersRepository->insertOrder($insert_data);
}

$orders = $ordersRepository->getOrderById($test_order_id);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>
<form action="" method="post">
    <label>state</label>: <input type="text" name="state" value="10" /><br />
    <label>shipping_fee</label>: <input type="text" name="shipping_fee" value="150" /><br />
    <label>shipping</label>: <input type="text" name="shipping" value="1" /><br />
    <label>arriving_date</label>: <input type="text" name="arriving_date" value="2019-07-31" /><br />
    <label>arriving_time</label>: <input type="text" name="arriving_time" value="1" /><br />
    <label>payment</label>: <input type="text" name="payment" value="999" /><br />
    <label>payment_result</label>: <input type="text" name="payment_result" value="OK" /><br />
    <label>user_id</label>: <input type="text" name="user_id" value="1" /><br />
    <label>has_reserve</label>: <input type="text" name="has_reserve" value="0" /><br />
    <label>comment</label>: <input type="text" name="comment" value="テストコメント" /><br />
    <label>shop_comment</label>: <input type="text" name="shop_comment" value="テストのショッピングコメント" /><br />
    <label>created_at</label>: <input type="text" name="created_at" value="2019-07-13" /><br />
    <label>modified_at</label>: <input type="text" name="modified_at" value="2019-07-13" /><br />
    <label>deleted_at</label>: <input type="text" name="deleted_at" value="" /><br />
    <input type="submit" name="order_submit" value="submit"><br />
</form>
<p>&nbsp;</p>
<table>
    <tr>
        <th>id</th>
        <th>state</th>
        <th>shipping_fee</th>
        <th>shipping</th>
        <th>arriving_date</th>
        <th>arriving_time</th>
        <th>payment</th>
        <th>payment_result</th>
        <th>user_id</th>
        <th>has_reserve</th>
        <th>comment</th>
        <th>shop_comment</th>
    </tr>
    <?php foreach ($orders as $order) { ?>
        <tr>
            <td><?= $order->id ?></td>
            <td><?= $order->state ?></td>
            <td><?= $order->shipping_fee ?></td>
            <td><?= $order->shipping ?></td>
            <td><?= $order->arriving_date ?></td>
            <td><?= $order->arriving_time ?></td>
            <td><?= $order->payment ?></td>
            <td><?= $order->payment_result ?></td>
            <td><?= $order->user_id ?></td>
            <td><?= $order->has_reserve ?></td>
            <td><?= $order->comment ?></td>
            <td><?= $order->shop_comment ?></td>
        </tr>
    <?php } ?>
</table>
<?php var_dump($orders); ?>
</body>
</html>