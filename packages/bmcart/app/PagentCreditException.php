<?php
/**
 * Created by PhpStorm.
 * User: k_otsuka
 * Date: 2019-06-08
 * Time: 11:46
 */

namespace Packages\bmcart\app;


class PagentCreditException extends \Exception
{
    /**
     * @var null
     */
    public $payment_id = null;

    /**
     * 処理結果 0=正常終了, 1=異常終了
     * @var null
     */
    public $resultStatus = null;

    /**
     * 異常終了時、レスポンスコードが取得できる
     * @var null
     */
    public $responseCode = null;

    /**
     * 異常終了時、レスポンス詳細が取得できる
     * @var null
     */
    public $responseDetail = null;

    public function __construct($error_data) {
        parent::__construct();
        $this->customException($error_data);
    }

    protected function customException($error_data) {
        $this->payment_id = $error_data['payment_id'];
        $this->resultStatus = $error_data['resultStatus'];
        $this->responseCode = $error_data['responseCode'];
        $this->responseDetail = $error_data['responseDetail'];
        $this->message = sprintf("Payment Credit Error: payment_id=%s, responseCode=%s", $this->payment_id, $this->responseCode);
    }

}