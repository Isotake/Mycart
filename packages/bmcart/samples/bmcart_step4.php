<?php

ini_set("display_errors", On);
error_reporting(E_ALL);

session_start();

require_once '../../../vendor/autoload.php';

use Packages\Bmcart\App\BmcartStep4;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\ChromePHPHandler;
use Packages\Bmcart\Error\CartException;

$log = new Logger('bm-cart');
$log->pushHandler(new StreamHandler('../../../logs/sql.log', Logger::DEBUG));
$log->pushHandler(new ChromePHPHandler(Logger::DEBUG));

$cart_project = 'mycart';
$cart_version = '2.0.0';
$debug_display = true;
$debug_message = 'このカートはテストです<br />クレジット決済は引き落としされますが、商品は届きません <i class="fas fa-grin-tongue" style="font-size: 21px;"></i>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $is_ajax = true;
} else {
    $is_ajax = false;
}

$request = [];
$request['is_ajax'] = $is_ajax;

//$request_card_token = '****';

$instance_data = [
    "cart_project" => $cart_project,
    "cart_version" => $cart_version,
    "debug_display" => $debug_display,
    "debug_message" => $debug_message,
    "request_data" => $request,
    "request_cart_error" => (isset($_SESSION[$cart_project]['request_cart_error'])) ? $_SESSION[$cart_project]['request_cart_error'] : null ,
    "login" => (isset($_SESSION[$cart_project]['login'])) ? $_SESSION[$cart_project]['login'] : null ,
    "basket" => (isset($_SESSION[$cart_project]['basket'])) ? $_SESSION[$cart_project]['basket'] : null ,
    "shipping_method" => (isset($_SESSION[$cart_project]['shipping_method'])) ? $_SESSION[$cart_project]['shipping_method'] : null ,
    "payment_method" => (isset($_SESSION[$cart_project]['payment_method'])) ? $_SESSION[$cart_project]['payment_method'] : null ,
    "usage_of_point" => (isset($_SESSION[$cart_project]['usage_of_point'])) ? $_SESSION[$cart_project]['usage_of_point'] : null ,
    "usepoint" => (isset($_SESSION[$cart_project]['usepoint'])) ? $_SESSION[$cart_project]['usepoint'] : null ,
    "cm" => (isset($_SESSION[$cart_project]['cm'])) ? $_SESSION[$cart_project]['cm'] : null ,
    "choicedate" => (isset($_SESSION[$cart_project]['choicedate'])) ? $_SESSION[$cart_project]['choicedate'] : null ,
    "cm2" => (isset($_SESSION[$cart_project]['cm2'])) ? $_SESSION[$cart_project]['cm2'] : null ,
    "address_data" => (isset($_SESSION[$cart_project]['address_data'])) ? $_SESSION[$cart_project]['address_data'] : null ,
    "remark" => (isset($_SESSION[$cart_project]['remark'])) ? $_SESSION[$cart_project]['remark'] : null ,
//    "card_token" => (isset($request_card_token)) ? $request_card_token : null ,
];

try {
    $bmcart = new BmcartStep4($instance_data);
    $bmcart->cartRun();
    unset($_SESSION[$cart_project]['request_cart_error']);
    $bmcart->template->setVars($bmcart->template->vars);
    $bmcart->template->show('mycart_step4.tpl.html');

    if ($bmcart->order_completed) {
        unset($_SESSION[$cart_project]['basket']);
        unset($_SESSION[$cart_project]['shipping_method']);
        unset($_SESSION[$cart_project]['payment_method']);
        unset($_SESSION[$cart_project]['usage_of_point']);
        unset($_SESSION[$cart_project]['usepoint']);
        unset($_SESSION[$cart_project]['cm']);
        unset($_SESSION[$cart_project]['choicedate']);
        unset($_SESSION[$cart_project]['cm2']);
        unset($_SESSION[$cart_project]['address_data']);
        unset($_SESSION[$cart_project]['remark']);
    }

//    var_dump($_SESSION[$cart_project]);

} catch (CartException $cartException) {
    $error_code = $cartException->getCode();
    $error_message = $cartException->getMessage();
    $error_http_status = $cartException->getHttpStatus();
    $error_redirect_url = $cartException->getRedirectUrl();

    switch ($error_code) {
        case 201:
            unset($_SESSION[$cart_project]['shipping_method']);
            $_SESSION[$cart_project]['request_cart_error']['shipping']['no_shipping_method'] = 1;
            break;
        case 211:
            unset($_SESSION[$cart_project]['shipping_method']);
            $_SESSION[$cart_project]['request_cart_error']['shipping']['incorrect_shipping_method'] = 1;
            break;
        case 301:
            unset($_SESSION[$cart_project]['payment_method']);
            $_SESSION[$cart_project]['request_cart_error']['payment']['no_payment_method'] = 1;
            break;
        case 311:
            unset($_SESSION[$cart_project]['payment_method']);
            $_SESSION[$cart_project]['request_cart_error']['payment']['incorrect_payment_method'] = 1;
            break;
        case 401:
            unset($_SESSION[$cart_project]['usage_of_point']);
            $_SESSION[$cart_project]['request_cart_error']['use_point'] = 1;
            break;
        case 411:
            unset($_SESSION[$cart_project]['usepoint']);
            $_SESSION[$cart_project]['request_cart_error']['usepoint_exceeded_error'] = 1;
            break;
        case 511:
            foreach ($bmcart->cart_error['stock_error'] as $item_id => $error_item) {
                if (isset($error_item['stock_price'])) { $_SESSION[$cart_project]['basket'][$item_id]['price'] = $error_item['stock_price']; }
                if (isset($error_item['stock_quantity'])) { $_SESSION[$cart_project]['basket'][$item_id]['quantity'] = $error_item['stock_quantity']; }
                if (isset($error_item['stock_point'])) { $_SESSION[$cart_project]['basket'][$item_id]['point'] = $error_item['stock_point']; }
            }
            $_SESSION[$cart_project]['request_cart_error']['stock_error'] = $bmcart->cart_error['stock_error'];
            break;
        default:
            break;
    }

    if ($request['is_ajax']) {
        $header_string = 'HTTP/1.0 ' . $error_http_status . ' ' . $error_redirect_url;
        header($header_string);
    } else {
        header('Location: ' . $error_redirect_url, true, $error_http_status);
    }
    exit;

} catch (\Exception $exception) {
    var_dump('init exception');
}
