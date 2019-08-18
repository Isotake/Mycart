<?php

ini_set("display_errors", On);
error_reporting(E_ALL);

require '../../../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager AS Capsule;
use Packages\Bmcart\Model\StocksRepository;
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

$item_id = 1;

$stocksRepository = new StocksRepository();

if (isset($_POST['item_stocks'])) {
    $item_id = $_POST['item_id'];
    $quantity = $_POST['stock_quantity'];
    $update_data[$item_id] = $quantity;
    $stocksRepository->updateStocks($update_data);
}

if (isset($_POST['item_reserve_stocks'])) {
    $item_id = $_POST['item_id'];
    $quantity = $_POST['reserve_stock_quantity'];
    $update_data[$item_id] = $quantity;
    $stocksRepository->updateReserveStocks($update_data);
}

$item = $stocksRepository->findById([$item_id]);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>
<form action="" method="post">
    在庫数 : <?= $item->stock ?>&nbsp;
    <input type="text" name="stock_quantity" value="0" />
    <input type="hidden" name="item_id" value="<?= $item_id ?>" />
    <input type="submit" name="item_stocks" value="submit"><br>
    <br>
    予約入荷数 : <?= $item->reserve_stock ?>&nbsp;
    <input type="text" name="reserve_stock_quantity" value="0" />
    <input type="hidden" name="item_id" value="<?= $item_id ?>" />
    <input type="submit" name="item_reserve_stocks" value="submit"><br>
</form>
<?php var_dump($item); ?>
</body>
</html>