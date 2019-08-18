<?php

ini_set("display_errors", On);
error_reporting(E_ALL);

require '../../../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager AS Capsule;
use Packages\Bmcart\Model\UserPointsRepository;
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

$test_user_id = 1;

$userPointsRepository = new UserPointsRepository();

if (isset($_POST['user_point_submit'])) {
    $UserPointsEntity = $userPointsRepository->updateUserPoint($test_user_id, (int)$_POST['user_point'], true);
}

$user_point = $userPointsRepository->getUserPoints($test_user_id);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>
<form action="" method="post">
    test_user_id : <?= $test_user_id ?><br />
    <input type="text" name="user_point" value="<?= $user_point->point ?>" />
    <input type="submit" name="user_point_submit" value="submit">
</form>
<?php var_dump($user_point); ?>
</body>
</html>