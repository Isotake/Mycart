<?php

namespace Packages\Mail\App;

use Packages\Mail\Core\Email;
use Packages\Mail\Core\Template;

class ContactForm {

    public $email = null;
    public $template = null;

    public function __construct (Email $email)
    {
        $this->email = $email;
        $this->template = new Template();
    }

    public function setMessage ($values, $tpl_file)
    {
        foreach ($values as $key => $value) {
            $this->template->$key = $value;
        }
        $content = $this->template->evaluate($tpl_file);
        $this->email->message($content);
    }

    public function send ()
    {
        mb_language("Japanese");
        mb_internal_encoding($this->email->charset);

        $subject = $this->email->encodeBase64($this->email->_subject);
        $from = sprintf("%s <%s>", $this->email->encodeBase64($this->email->_from['name']), $this->email->_from['mail']);
        $pfrom = "-f$from";

        $to = $this->email->getHeaderAddress("_to");
        $message = $this->email->encode(str_replace("\n", "\r\n", $this->email->_message));
        $cc = $this->email->getHeaderAddress("_cc");
        $bcc = $this->email->getHeaderAddress("_bcc");

        $headers = "MIME-Version: 1.0 \n";
        $headers .= "Content-Type: text/html;charset=iso-2022-jp \n";
        $headers .= "Content-Transfer-Encoding: 7bit\n";
        $headers .= "From: {$from}\n";
        $headers .= "Reply-To: {$from}\n";
        if (!empty($cc)) {
            $headers .= "Cc: {$cc}\n";
        }
        if (!empty($bcc)) {
            $headers .= "Bcc: {$bcc}\n";
        }
        mail($to, $subject, $message, $headers, $pfrom);
    }

}
