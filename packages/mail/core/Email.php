<?php

namespace Packages\Mail\Core;

class Email {

    const LINE_LENGTH_MUST = 998;
    const MESSAGE_HTML = 'html';
    const MESSAGE_TEXT = 'text';
    const EMAIL_PATTERN = '/^((?:[\p{L}0-9.!#$%&\'*+\/=?^_`{|}~-]+)*@[\p{L}0-9-.]+)$/ui';

    static public $_contentTypeCharset = [
        'ISO-2022-JP-MS' => 'ISO-2022-JP',
    ];

    /* */
    public $_emailPattern = self::EMAIL_PATTERN;
    public $_subject = '';
    public $_from = [];
    public $_to = [];
    public $_cc = [];
    public $_bcc = [];
    public $_message = '';
    public $charset = 'UTF-8';
    public $mail_charset = 'ISO-2022-JP-MS';
    public $header_charset = 'ISO-2022-JP';

    static public function getHeaderCharset($charset) {
        if (array_key_exists($charset, self::$_contentTypeCharset)) {
            return self::$_contentTypeCharset[$charset];
        }
        return $charset;
    }

    static public function getEmailAssoc($email, $name = null) {
        if ($name === null || empty($name)) {
            return ['mail' => $email, 'name' => $email];
        }
        return ['mail' => $email, 'name' => $name];
    }

    public function encodeBase64($value) {
        $restore = mb_internal_encoding();
        mb_internal_encoding($this->charset);
        $_s = mb_encode_mimeheader($value, $this->header_charset, 'B'); // base64
        mb_internal_encoding($restore);
        return $_s;
    }

    public function encode($value) {
        return mb_convert_encoding($value, $this->mail_charset, $this->charset);
    }

    public function wrapMessage($message, $wrapLength = self::LINE_LENGTH_MUST) {
        $cut = ($wrapLength == self::LINE_LENGTH_MUST);

        $text = str_replace(["\r\n", "\r"], "\n", $message);
        $lines = explode("\n", $text);
        $formatted = [];
        foreach ($lines as $line) {
            if (empty($line) && $line !== '0') {
                $formatted[] = "\r\n";
                continue;
            }
            if (strlen($line) < $wrapLength) {
                $formatted[] = $line . "\r\n";
                continue;
            }
            $formatted = array_merge($formatted, explode("\n", wordwrap($line, $wrapLength, "\r\n", $cut)));
        }
        return implode('', $formatted);
    }

    public function validateEmail($email) {
        if ($this->_emailPattern === null) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return;
            }
        } elseif (preg_match($this->_emailPattern, $email)) {
            return;
        }
        throw new InvalidArgumentException(sprintf('Invalid email: "%s"', $email));
    }

    public function _setEmails($varName, $email) {
        $list = [];
        foreach ($email as $key => $value) {
            if (is_array($value)) {
                if (array_key_exists('mail', $value)) {
                    if (array_key_exists('name', $value)) {
                        $list[] = self::getEmailAssoc($value['mail'], $value['name']);
                    } else {
                        $list[] = self::getEmailAssoc($value['mail']);
                    }
                } else {
                    throw new Exception("'mail' key not exist in array.");
                }
            } else {
                $this->validateEmail($value);
                $list[] = self::getEmailAssoc($value);
            }
        }
        $this->{$varName} = $list;
    }

    public function setEmail($varName, $email, $name) {

        $this->{$varName} = [];
        if (!is_array($email)) {
            try {
                $this->validateEmail($email);
                $this->{$varName}[] = self::getEmailAssoc($email, $name);
            } catch (InvalidArgumentException $ex) {
                
            }
        } else {
            $this->_setEmails($varName, $email);
        }

        return;
    }

    public function getHeaderAddress($varName) {
        $list = [];
        foreach ($this->{$varName} as $m) {
            $list[] = sprintf("%s <%s>", $this->encodeBase64($m['name']), $m['mail']);
        }
        return implode(',', $list);
    }

    public function message($message = null) {
        if ($message === null) {
            return $this->_message;
        }
        $this->_message = $this->wrapMessage($message);
    }

    public function subject($subject = null) {
        if ($subject === null) {
            return $this->_subject;
        }
        $this->_subject = $subject;
    }

    public function bcc($email = null, $name = null) {
        if ($email === null) {
            return $this->_bcc;
        }
        $this->setEmail('_bcc', $email, $name);
    }

    public function cc($email = null, $name = null) {
        if ($email === null) {
            return $this->_cc;
        }
        $this->setEmail('_cc', $email, $name);
    }

    public function to($email = null, $name = null) {
        if ($email === null) {
            return $this->_to;
        }
        $this->setEmail('_to', $email, $name);
    }

    public function from($email = null, $name = null) {
        if ($email === null) {
            return $this->_from;
        }
        try {
            $this->validateEmail($email);
            $this->_from = [];
            $this->_from = self::getEmailAssoc($email, $name);
        } catch (InvalidArgumentException $ex) {
            echo $ex;
        }
    }

    public function send() {

        mb_language("Japanese");
        mb_internal_encoding($this->charset);

        $subject = $this->encodeBase64($this->_subject);
        $from = sprintf("%s <%s>", $this->encodeBase64($this->_from['name']), $this->_from['mail']);
        $pfrom = "-f$from";

        $to = $this->getHeaderAddress("_to");
        $message = $this->encode(str_replace("\n", "\r\n", $this->_message));
        $cc = $this->getHeaderAddress("_cc");
        $bcc = $this->getHeaderAddress("_bcc");

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

    public function __construct($charset = "UTF-8", $mail_charset = "ISO-2022-JP-MS") {
        $this->charset = $charset;
        $this->mail_charset = $mail_charset;
        $this->header_charset = self::getHeaderCharset($mail_charset);
    }

}
