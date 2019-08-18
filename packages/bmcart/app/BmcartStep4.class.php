<?php

namespace Packages\Bmcart\App;

use Packages\Bmcart\Model\ItemsRepository;
use Packages\Bmcart\Model\UsersRepository;
use Packages\Bmcart\Model\UserPointsRepository;
use Packages\Bmcart\Model\OrdersRepository;
use Packages\Bmcart\Model\OrderDetailsRepository;
use Packages\Bmcart\Model\PrePointsRepository;
use Packages\Bmcart\Model\PrePointDetailsRepository;
use Packages\Bmcart\Model\UserPointLogsRepository;
use Packages\Bmcart\Model\StocksRepository;

use Packages\Payment\Paygent\Credit\PaygentCredit;

class BmcartStep4 extends Bmcart
{
	public function __construct($instance_data)
	{
		parent::__construct($instance_data);

		$this->items_repository = new ItemsRepository();
		$this->stocks_repository = new StocksRepository();

		$this->user_repository = new UsersRepository();

		$this->orders_repository = new OrdersRepository();
		$this->order_details_repository = new OrderDetailsRepository();

		$this->pre_points_repository = new PrePointsRepository();
		$this->pre_point_details_repository = new PrePointDetailsRepository();

		$this->user_points_repository = new UserPointsRepository();
		$this->user_point_details_repository = new UserPointLogsRepository();

	}

    public function init ()
    {

        $this->isMobile = $this->detect->isMobile();

        $this->setRequestCartError();

        $this->loginCheck();
        $this->setUserEntity();
        $this->setAvailablePoint();

        $this->basketCheck();
        $this->setItemEntities();

        $this->shippingCheck();
        $this->setShippingMethod();

        $this->paymentCheck();
        $this->setPaymentMethod();

        $this->pointCheck();
        $this->setUsageOfPoint();
        $this->setUsePoint();

        $this->setAddress();
        $this->setShippingDatetime();
        $this->setRemark();
        $this->setComment();
    }

    public function execute ()
    {

        $this->calcShippingFee();
		$this->setMaxUsablePoint();
        if ($this->usepoint) {
            $this->setCartPoint();
        }

        // 在庫チェック

        $db_connection = $this->db->getConnection();
        $db_connection->beginTransaction();
        try {

            // 商品情報チェック
            try {
                $this->stockErrorCheckForUpdate();
                unset($_SESSION['bmcart_error']['stock_error']);
            } catch (\Exception $exception) {
                $_SESSION['bmcart_error']['stock_error'] = $this->stock_error;
                $this->log->addDebug($exception->getMessage());
                header('Location: bmcart_step1.php');
                exit;
            }

            // 伝票処理
            $insert_order_data = [
                'state' => 10 ,
                'payment_result' => '',
                'shop_comment' => '',
            ];
            $resultOrder = $this->executeOrder($insert_order_data);
            $order_id = $resultOrder->id;
            if (!$order_id) {
                throw new \Exception('no order_id');
            }

            // 詳細伝票処理
            $insert_orderdetails_data = [];
            foreach ($this->basket as $item_id => $basket_item) {
                $price = $basket_item['price'];
                $point = $basket_item['point'];
                $insert_orderdetails_data[] = [
                    'order_id' => $order_id,
                    'item_id' => $item_id,
                    'user_id' => $this->user_entity['user_id'],
                    'price' => $price,
                    'number' => $basket_item['quantity'],
                    'point' => $point
                ];
            }
            $this->executeOrderDetails($insert_orderdetails_data);

            // 決済処理
            if ($this->payment_method === self::CREDIT) {
                $resultPayment = $this->executeCredit();
            } elseif ($this->payment_method === self::DELIVERY) {
            } elseif ($this->payment_method === self::BANK) {
            }

            // 仮ポイント処理
            $this->executePrePoints($order_id);
            $pre_point_id = $this->getUserGetPointLastInsertId($order_id);
            if (!$pre_point_id) {
                throw new \Exception('no pre_point_id');
            }

            //仮ポイント詳細処理
            $order_details_id_data = [];
            $card_point_data = [];
            foreach ($this->item_entities as $item_entity) {
                $order_details_id_data[] = $item_entity['item_id']; //todo item_idではなく、order_details_id
                $card_point_data[] = $item_entity['point'];
            }
            $this->executePrePointDetails($order_id, $pre_point_id, $order_details_id_data, $card_point_data);

            //実ポイント処理
            $this->executeUserPoints();

            //実ポイント詳細処理
            $this->executeUserPointDetails($order_id);

            // 在庫数アップデート
            foreach ($this->basket as $item_id => $basket_item) {
                $kind = $this->item_entities[$item_id]['is_reserve'];
                if ($this->item_entities[$item_id]['is_reserve']) {
                    $this->executeReserveStocks($item_id, $basket_item['quantity']);
                } else {
                    $this->executeStocks($item_id, $basket_item['quantity']);
                }
            }

            $db_connection->commit();

        } catch (PagentCreditException $exception) {
            $db_connection->rollBack();
            $this->log->addError($exception->getMessage());
            $this->log->addError(json_encode($this, JSON_UNESCAPED_UNICODE));
            $this->template->render('errors/credit_error.html', $exception);
            exit();
        } catch (\Exception $exception) {
//            $db_connection->rollBack();
            $this->log->addError($exception->getMessage());
            $this->log->addError(json_encode($this, JSON_UNESCAPED_UNICODE));
            $this->template->render('errors/cart_error.html', $exception);
            exit();
        }

        // メール送信

        $this->order_completed = true;

    }

    public function vars()
    {

        $cart_vars = [
            "cart_project" => $this->instance_data['cart_project'],
            "cart_version" => $this->instance_data['cart_version'],
            "debug_display" => $this->instance_data['debug_display'],
            "debug_message" => $this->instance_data['debug_message'],

            "basket" => $this->basket,
            "item_entities" => $this->item_entities->toArray(),
            "user_entity" => $this->user_entity->toArray(),
            "result_total" => $this->result_total,
            "result_totalnum" => $this->result_totalnum,
            "result_totalpoint" => $this->result_totalpoint,
            "result_mail_allowed" => $this->result_mail_allowed,
            "result_has_reserve" => $this->result_has_reserve,
            "result_over_discount_1st" => $this->result_over_discount_1st,
            "result_over_discount_2nd" => $this->result_over_discount_2nd,
            "result_yupacket_fee" => $this->result_yupacket_fee,
            "result_nekopos_fee" => $this->result_nekopos_fee,
            "result_sagawa_fee" => $this->result_sagawa_fee,
            "result_yamato_fee" => $this->result_yamato_fee,
            "result_yupack_fee" => $this->result_yupack_fee,
            "delivery_fee" => self::DELIVERY_FEE,
            "request_cart_error" => $this->request_cart_error,
            "morebuy_message" => $this->morebuy_message,
            "shipping_method_ids" => $this->shipping_method_ids,
            "shipping_methods" => $this->shipping_methods,
            "shipping_method" => $this->shipping_method,
            "shipping_timetable" => $this->shipping_timetable,
            "excharge" => $this->excharge,
            "total_price" => $this->total_price,
            "usepoint" => $this->usepoint,
            "max_usable_point" => $this->max_usable_point,
            "payment_method_ids" => $this->payment_method_ids,
            "payment_methods" => $this->payment_methods,
            "payment_method" => $this->payment_method,
            "user_get_point" => $this->user_get_point,
            "user_updated_point" => $this->user_updated_point,
            "address_ok" => $this->address_ok,
            "address_postcode1" => $this->address_postcode1,
            "address_postcode2" => $this->address_postcode2,
            "address_prefecture" => $this->address_prefecture,
            "address_city" => $this->address_city,
            "address_town" => $this->address_town,
            "address_address" => $this->address_address,
            "address_apartment" => $this->address_apartment,
            "shipping_datetime" => $this->shipping_datetime,
            "shipping_choicedate" => $this->shipping_choicedate,
            "shipping_choicetime" => $this->shipping_choicetime,
            "comment" => $this->comment,

            'title' => 'STEP4 | Bigwebカート',
            'load_js' => [
            ],
            'custom_js' => null,
            'step_number' => 4,
            'body_class' => ($this->isMobile) ? 'mobile step1' : 'pc step4' ,
        ];

        $htmlescape_whitelist = [
            'morebuy_message',
            'debug_message',
        ];

        foreach ($cart_vars as $key => $value) {
            if (is_array($value)) {
                $this->template->setVars($key, $value);
            } else {
                if (in_array($key, $htmlescape_whitelist)) {
                    $this->template->setVars($key, $value);
                } else {
                    $this->template->setVars($key, $this->template->h($value));
                }
            }
        }

//todo
//        $this->template->setVars([
//            "cart_project" => $this->instance_data['cart_project'],
//            "cart_version" => $this->instance_data['cart_version'],
//            "debug_display" => $this->instance_data['debug_display'],
//            "debug_message" => $this->instance_data['debug_message'],
//        ]);
//
//        $this->template->setVars([
//            "basket" => $this->basket,
//            "item_entities" => $this->item_entities->toArray(),
//            "user_entity" => $this->user_entity->toArray(),
//            "result_total" => $this->result_total,
//            "result_totalnum" => $this->result_totalnum,
//            "result_totalpoint" => $this->result_totalpoint,
//            "result_mail_allowed" => $this->result_mail_allowed,
//            "result_has_reserve" => $this->result_has_reserve,
//            "result_over_discount_1st" => $this->result_over_discount_1st,
//            "result_over_discount_2nd" => $this->result_over_discount_2nd,
//            "result_yupacket_fee" => $this->result_yupacket_fee,
//            "result_nekopos_fee" => $this->result_nekopos_fee,
//            "result_sagawa_fee" => $this->result_sagawa_fee,
//            "result_yamato_fee" => $this->result_yamato_fee,
//            "result_yupack_fee" => $this->result_yupack_fee,
//            "delivery_fee" => self::DELIVERY_FEE,
//            "request_cart_error" => $this->request_cart_error,
//            "morebuy_message" => $this->morebuy_message,
//            "shipping_method_ids" => $this->shipping_method_ids,
//            "shipping_methods" => $this->shipping_methods,
//            "shipping_method" => $this->shipping_method,
//            "shipping_timetable" => $this->shipping_timetable,
//            "excharge" => $this->excharge,
//            "total_price" => $this->total_price,
//            "usepoint" => $this->usepoint,
//            "max_usable_point" => $this->max_usable_point,
//            "payment_method_ids" => $this->payment_method_ids,
//            "payment_methods" => $this->payment_methods,
//            "payment_method" => $this->payment_method,
//            "user_get_point" => $this->user_get_point,
//            "user_updated_point" => $this->user_updated_point,
//            "address_ok" => $this->address_ok,
//            "address_postcode1" => $this->address_postcode1,
//            "address_postcode2" => $this->address_postcode2,
//            "address_prefecture" => $this->address_prefecture,
//            "address_city" => $this->address_city,
//            "address_town" => $this->address_town,
//            "address_address" => $this->address_address,
//            "address_apartment" => $this->address_apartment,
//            "shipping_datetime" => $this->shipping_datetime,
//            "shipping_choicedate" => $this->shipping_choicedate,
//            "shipping_choicetime" => $this->shipping_choicetime,
//            "comment" => $this->comment,
//        ]);
//
//        $this->template->setVars([
//            'title' => 'STEP4 | Bigwebカート',
//            'load_js' => [
//            ],
//            'custom_js' => null,
//            'step_number' => 4,
//            'body_class' => ($this->isMobile) ? 'mobile step1' : 'pc step4' ,
//        ]);

    }


    /**
     * Stock Error Check For Update
     */
    public function stockErrorCheckForUpdate ()
    {
        $item_array = array_keys($this->basket);
        $results = $this->items_repository->findByIdForExclusiveLock($item_array)->keyBy('item_id');
        foreach ($this->basket as $item_id => $basket_item) {
            if ($basket_item['quantity'] > $results[$item_id]['stock']) {
                $this->cart_error['stock_error'][$item_id] = [
                    'basket_quantity' => $basket_item['quantity'],
                    'stock_quantity' => $results[$item_id]['stock'],
                ];
            }

            if ($basket_item['price'] != $results[$item_id]['price']) {
                $this->cart_error['stock_error'][$item_id] = [
                    'basket_price' => $basket_item['price'],
                    'stock_price' => $results[$item_id]['price'],
                ];
            }

            if ($basket_item['point'] != $results[$item_id]['point']) {
                $this->cart_error['stock_error'][$item_id] = [
                    'basket_item_point' => $basket_item['point'],
                    'stock_item_point' => $results[$item_id]['point'],
                ];
            }
        }

        if ($this->cart_error['stock_error']) {
            throw new \Exception('Stock Error');
        }

    }


    /**
     * Voucher number
     */
    public function makeVoucher($store_code)
    {
        $fpath = "../../../m.txt";
        $handle=fopen($fpath, "r");
        $contents = fread($handle, filesize($fpath));
        fclose($handle);

        list($date, $f)=explode("\n", $contents);
        if($date== date("Y-m-d")) $f++;
        else $f=1;

        $voucher_num= $store_code ."-".(date("Ymd"))."-".$f;

        $handle=fopen($fpath, "w");
        $fc=date("Y-m-d")."\n".$f;
        fwrite($handle, $fc);

        return $voucher_num;
    }


    /**
     * Credit
     */
    public function executeCredit()
    {
        $credit_data = [
            'card_token' => $this->instance_data['card_token'],
            'security_code_token' => 0,
            'security_code_use' => 1,
            'order_id' => '123',
            'total_price' => $this->total_price,
        ];

        $pagent_credit = new PaygentCredit();
//        $pagent_credit->execute($credit_data);
        $pagent_credit->dummyExecute($credit_data);

        // 決済エラー処理
        if ($pagent_credit->payment_id == "" || $pagent_credit->payment_id == 0 || $pagent_credit->resultStatus > 0) {
            $error_data = [
                'payment_id' => $pagent_credit->payment_id,
                'resultStatus' => $pagent_credit->resultStatus,
                'responseCode' => $pagent_credit->responseCode,
                'responseDetail' => $pagent_credit->responseDetail,
            ];
            throw new PagentCreditException($error_data);
        }

        return $pagent_credit;
    }

    /**
     * OrderNew
     */
    public function executeOrder($insert_order_data)
    {
        $insert_data = [
            'state' => $insert_order_data['state'],
            'shipping_fee' => $this->excharge ,
            'shipping' => $this->shipping_method,
            'arriving_date' => $this->shipping_choicedate,
            'arriving_time' => $this->shipping_choicetime,
            'payment' => $this->payment_method,
            'payment_result' => $insert_order_data['payment_result'],
            'user_id' => $this->user_entity['user_id'],
            'has_reserve' => $this->result_has_reserve,
            'comment' => $this->comment,
            'shop_comment' => $insert_order_data['shop_comment'],
        ];

        $result = $this->orders_repository->insertOrder($insert_data);
        if ($result) {
            return $result;
        } else {
            throw new \Exception('OrderNew Insert Error');
        }
    }

    /**
     * OrderCardsNew
     */
    public function executeOrderDetails($insert_orderdetails_data)
    {
        $result = $this->order_details_repository->insertOrderDetails($insert_orderdetails_data);
        if ($result) {
            return $result;
        } else {
            throw new \Exception('OrderDetails Insert Error');
        }
    }

    /**
     * PrePoints
     */
    public function executePrePoints($order_id)
    {
        $order_id = $order_id;
        $user_id = $this->user_entity['users_id'];
        $operator_id = $this->user_entity['legacy_user_id'];
        $point_get = $this->user_get_point;
        $use_point = $this->usepoint;

        $result = $this->pre_points_repository->insertPrePoints($order_id, $user_id, $operator_id, $point_get, $use_point);
        if ($result) {
            return $result;
        } else {
            throw new \Exception('PrePoints Insert Error');
        }
    }

    public function getUserGetPointLastInsertId($order_id)
    {
        $result = $this->pre_points_repository->getUserGetPointLastInsertId($order_id);
        if ($result->id) {
            return $result->id;
        } else {
            throw new \Exception('GetlastInsertId Error');
        }
    }

    public function executePrePointDetails($order_id, $pre_point_id, $order_details_id, $card_point)
    {
        $order_id = $order_id;
        $user_id = $this->user_entity['user_id'];
        $pre_point_id = $pre_point_id;

        $order_details_id_data = $order_details_id;
        $card_point_data = $card_point;
        $order_details_ids = [];
        foreach ($order_details_id_data as $order_detail_id) {
            foreach ($card_point_data as $card_point) {
                $order_details_ids[$order_detail_id] = [
                    'id' => $order_detail_id,
                    'card_point' => $card_point
                ];
            }
        }

        $result = $this->pre_point_details_repository->insertPrePointDetails($order_id, $user_id, $pre_point_id, $order_details_ids);
        if ($result) {
            return $result;
        } else {
            throw new \Exception('PrePointDetails Insert Error');
        }
    }

    /**
     * UserPoints
     */
    public function executeUserPoints()
    {
        $user_id = $this->user_entity['user_id'];
        $updated_point = ($this->user_updated_point) ? $this->user_updated_point : 0 ;

        $result = $this->user_points_repository->updateUserPoint($user_id, $updated_point, true);
        if ($result) {
            return $result;
        } else {
            throw new \Exception('UserPoints Insert Error');
        }
    }

    public function executeUserPointDetails($order_id)
    {

        $user_id = $this->user_entity['user_id'];
        $operator_id = $user_id;
        $user_point = $this->user_get_point - $this->usepoint;
        $user_point_reason_id = 2;
        $comment = 'order_ID: ' . $order_id;

        $result = $this->user_point_details_repository->insertUserPointLogs($user_id, $operator_id, $user_point, $user_point_reason_id, $comment);
        if ($result) {
            return $result;
        } else {
            throw new \Exception('UserPointDetails Insert Error');
        }
    }

    /**
     * ItemStocks
     */
    public function executeStocks($item_id, $quantity)
    {
        $update_data[$item_id] = $quantity;
        $this->stocks_repository->updateStocks($update_data);
    }

    /**
     * Items
     */
    public function executeReserveStocks($item_id, $quantity)
    {
        $update_data[$item_id] = $quantity;
        $this->stocks_repository->updateReserveStocks($update_data);
    }

}
