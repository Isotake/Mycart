<?php

require '../core/Email.php';
require '../app/ContactForm.php';

use Packages\Mail\Core\Email;
use Packages\Mail\App\ContactForm;

ini_set("display_errors", 'On');
error_reporting(E_ALL);

session_start();

function getSenderName ($value) {
    return $value;
}

function getSenderAddress ($value) {
    return $value;
}

function getSenderMessage ($value) {
    return $value;
}

$already_sent = false;
if (isset($_SESSION['already_sent']) && $_SESSION['already_sent']) {
    $already_sent = true;
    unset($_SESSION['already_sent']);
}

if (isset($_POST['contactform-submit'])) {
    $subject = "メールフォーム";

    $contactform_name = getSenderName($_POST['contactform-name']);
    $contactform_address = getSenderAddress($_POST['contactform-address']);
    $contactform_message = getSenderMessage($_POST['contactform-message']);
    $message_parameters = [
        'contactform_name' => $contactform_name,
        'contactform_address' => $contactform_address,
        'contactform_message' => $contactform_message,
    ];

    $email = new Email();
    $contactform = new ContactForm($email);
    $contactform->email->from('k.otsuka@daishokagaku.com');
    $contactform->email->to('k.otsuka@daishokagaku.com');
    $contactform->email->subject($subject);
    $contactform->setMessage($message_parameters, 'contactform.tpl.html');
    $contactform->send();
    $_SESSION['already_sent'] = true;
    header('Location: ./contactform.php');
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
        <label for="name">お名前</label>
        <div>
            <input type="text" id="name" placeholder="" name="contactform-name" required>
        </div>
    </div>
    <div>
        <label for="address">メールアドレス</label>
        <div>
            <input type="email" id="address" placeholder="" name="contactform-address" required>
        </div>
    </div>
    <div>
        <label for="message">本文</label>
        <div>
            <textarea id="message" rows="3" name="contactform-message" required></textarea>
        </div>
    </div>
    <div>
        <div>
            <button type="submit" name="contactform-submit" value="submit">送信</button>
        </div>
    </div>
</form>
