<?php
/**
 * Created by PhpStorm.
 * User: k_otsuka
 * Date: 2019-06-08
 * Time: 11:46
 */

namespace Packages\bmcart\error;


class CartException extends \Exception
{
    public $http_status;

    public $redirect_url;

    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->customException($message, $code, $previous);
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function setHttpStatus ($http_status)
    {
        $this->http_status = $http_status;
    }

    public function getHttpStatus ()
    {
        return $this->http_status;
    }

    public function setRedirectUrl ($redirect_url)
    {
        $this->redirect_url = $redirect_url;
    }

    public function getRedirectUrl ()
    {
        return $this->redirect_url;
    }

    protected function customException($message, $code, $previous)
    {
        switch ($code) {
            case 101:
                $this->setHttpStatus(307);
                $this->setRedirectUrl('errors/noitem_error.html');
                break;
            case 201:
            case 211:
            case 301:
            case 311:
            case 411:
            case 511:
                $this->setHttpStatus(307);
                $this->setRedirectUrl('bmcart_step1.php');
                break;
            case 401:
                $this->setHttpStatus(307);
                $this->setRedirectUrl('bmcart_step2.php');
                break;
            case 601:
                $this->setHttpStatus(307);
                $this->setRedirectUrl('errors/login_error.html');
                break;
            default:
                break;
        }
    }

}