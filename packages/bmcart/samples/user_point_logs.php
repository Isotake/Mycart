<?php

ini_set("display_errors", On);
error_reporting(E_ALL);

require '../../../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager AS Capsule;
use Packages\Bmcart\Model\UserPointLogsRepository;
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
$test_operator_id = $test_user_id;

$userPointLogsRepository = new UserPointLogsRepository();

if (isset($_POST['user_point_submit'])) {
    $user_id = $_POST['user_id'];
    $operator_id = $_POST['operator_id'];
    $user_point = $_POST['user_point'];
    $user_point_reason_id = $_POST['user_point_reason_id'];
    $comment = $_POST['comment'];

    $result = $userPointLogsRepository->insertUserPointLogs($user_id, $operator_id, $user_point, $user_point_reason_id, $comment);
}

$user_point_logs = $userPointLogsRepository->getUserPointLogs($test_user_id);

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
        user_id : <?= $test_user_id ?><br />
        operator_id : <?= $test_operator_id ?><br />
        <input id="user_id" type="hidden" name="user_id" value="<?= $test_user_id ?>" />
        <input id="operator_id" type="hidden" name="operator_id" value="<?= $test_operator_id ?>" />
    </div>
    <div>
        <label for="user_point">実ポイント</label>
        <input id="user_point" type="text" name="user_point" required />
    </div>
    <div>
        user_point_reason_id : 2 (ポイント使用)
        <input id="user_point_reason_id" type="hidden" name="user_point_reason_id" value="2" />
    </div>
    <div>
        <label for="comment">コメント</label>
        <input id="comment" type="text" name="comment" />
    </div>
    <div>
        <input type="submit" name="user_point_submit" value="submit">
    </div>
</form>
<p>&nbsp;</p>
<table>
    <tr>
        <th>user_id</th><th>operator_id</th><th>point</th><th>user_point_reason</th><th>comment</th><th>created_at</th><th>modified_at</th>
    </tr>
    <?php foreach ($user_point_logs as $user_point_log) { ?>
        <tr>
            <td><?= $user_point_log->user_id ?></td>
            <td><?= $user_point_log->operator_id ?></td>
            <td><?= $user_point_log->point ?></td>
            <td><?= $user_point_log->user_point_reason ?></td>
            <td><?= $user_point_log->comment ?></td>
            <td><?= $user_point_log->created_at ?></td>
            <td><?= $user_point_log->modified_at ?></td>
        </tr>
    <?php } ?>
</table>
<?php
var_dump($user_point_logs);
?>
</body>
</html>