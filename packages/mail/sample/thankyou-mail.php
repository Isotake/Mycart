<?php

require '../../../vendor/autoload.php';

use Packages\Mail\Core\Email;
use Packages\Mail\App\ContactForm;

ini_set("display_errors", 'On');
error_reporting(E_ALL);

//session_start();

$already_sent = false;
if (isset($_SESSION['already_sent']) && $_SESSION['already_sent']) {
    $already_sent = true;
    unset($_SESSION['already_sent']);
}

if (isset($_POST['contactform-submit'])) {
    $subject = "メールフォーム";

    $message_parameters = [
    ];

    $email = new Email();
    $contactform = new ContactForm($email);
    $contactform->email->from('k.otsuka@daishokagaku.com');
    $contactform->email->to('k.otsuka@daishokagaku.com');
    $contactform->email->subject($subject);
    $contactform->setMessage($message_parameters, 'thankyou-mail.tpl.html');
    $contactform->send();
    $_SESSION['already_sent'] = true;
    header('Location: ./thankyou-mail.php');
    exit;
}

?>

<?php
if ($already_sent) {
    echo 'Your Mail has been already sent successfully...';
}
?>


<form acrtion="" method="post" name="contactform">
    <div>
        <div>
            <button type="submit" name="contactform-submit" value="submit">送信</button>
        </div>
    </div>
</form>
