<?php

require '../core/Email.php';
require '../app/GroupMail.php';

use Packages\Mail\Core\Email;
use Packages\Mail\App\GroupMail;

ini_set("display_errors", 'On');
error_reporting(E_ALL);

session_start();

$already_sent = false;
if (isset($_SESSION['already_sent']) && $_SESSION['already_sent']) {
    $already_sent = true;
    unset($_SESSION['already_sent']);
}

if (isset($_POST['groupmail-submit'])) {
    $subject = "グループメール";

    $clients = [
        'k_otsuka0201@yahoo.co.jp',
        'bleu-bleut@cream.plala.or.jp',
        'k.otsuka0201@gmail.com',
    ];

    $message_parameters = [
    ];

    $email = new Email();
    $groupmail = new GroupMail($email);
    $groupmail->email->from('k.otsuka@daishokagaku.com');
    $groupmail->setClients($clients);
    $groupmail->email->subject($subject);
    $groupmail->setMessage($message_parameters, 'groupmail.tpl.html');
    $groupmail->sender();
    $_SESSION['already_sent'] = true;
    header('Location: ./groupmail.php');
    exit;
}

?>

<?php
if ($already_sent) {
    echo 'Group Mail has been already sent successfully...';
}
?>


<form acrtion="" method="post" name="groupmail">
    <div>
        <div>
            <button type="submit" name="groupmail-submit" value="submit">送信</button>
        </div>
    </div>
</form>
