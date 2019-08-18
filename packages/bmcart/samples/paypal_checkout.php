<?php

ini_set("display_errors", On);
error_reporting(E_ALL);

session_start();

require_once '../../../vendor/autoload.php';

use Packages\Bmcart\App\BmcartStep1;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\ChromePHPHandler;

$log = new Logger('bm-cart');
$log->pushHandler(new StreamHandler('../../../logs/sql.log', Logger::DEBUG));
$log->pushHandler(new ChromePHPHandler(Logger::DEBUG));

echo 'paypal_checkout';

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>paypal_checkout.php</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://www.paypalobjects.com/api/checkout.js"></script>
    <script>
        paypal.Button.render({
            env: "sandbox", // production or sandbox
            commit: true, // This will add the transaction amount to the PayPal button
            locale: 'ja', // js_JP or en_US
            style: {
                size: 'responsive',
                color: 'gold',
                shape: 'rect',
                label: 'checkout'
            },
            payment: function (data, actions) {
                let acceptance_id = 123;
                return actions.request.post('/acceptances/create-payment/' + acceptance_id)
                    .then(function (res) {
                        //console.log(res);
                        return res.id;
                    });
            },
            onAuthorize: function (data, actions) {
                // You'll implement this callback later when you're
                // ready to execute the payment
                //console.log(data);
                $("#intent").val(data["intent"]);
                $("#orderid").val(data["orderID"]);
                $("#payerid").val(data["payerID"]);
                $("#paymentid").val(data["paymentID"]);
                $("#paymenttoken").val(data["paymentToken"]);
                //disabledBack();
                //var form = $("#paypal-transaction");
                //form.submit();
            },
            onCancel: function (data, actions) {
                //console.log(data);
                //$("#paypal-cancel").show();
                //enabledBack();
                //return actions.redirect();
            },
            onError: function (err) {
                // Show an error page here, when an error occurs
                //console.log(err);
                //$("#paypal-error").show();
                //enabledBack();
            }
        }, '#paypal-button');
        function enabledBack() {
            //var b = $("#submit-back");
            //$("#submit-back").css({"background-color": "white", "color": "#1723b1"});
            //b.val('<?= __("Back") ?>');
            // var form = $('#form-back');
            // form.submit(function (e) {
            //     form.off('submit');
            //     form.submit();
            // });
        }
        function disabledBack() {
            //var b = $("#submit-back");
            //b.val('<?= __("Payment processing") ?>') + "...";
            //b.css({"background-color": "#1723b1", "color": "white"});
            //waitingDialog.show('<?//= __('Payment processing') ?>//');
            //var form = $('#form-back');
            //form.submit(function (e) {
            //    return false;
            //});
        }
    </script>
</head>
<body>
<form method="post" accept-charset="utf-8" id="paypal-transaction" action="/acceptances/payment-paypal">
    <div class="acceptances-entry-form">
        <div style="margin-bottom: 2.0em;"><span>下記のボタンをクリックしてPaypal決済を完了してください。</span></div>
        <div id="paypal-button"></div>
        <input type="hidden" name="intent" id="intent">
        <input type="hidden" name="orderID" id="orderid">
        <input type="hidden" name="payerID" id="payerid">
        <input type="hidden" name="paymentID" id="paymentid">
        <input type="hidden" name="paymentToken" id="paymenttoken">
    </div>
</form>
</div>
</body>
</html>
