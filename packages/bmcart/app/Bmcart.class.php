<?php

namespace Packages\Bmcart\App;

use Illuminate\Database\Capsule\Manager AS Capsule;
use Detection\MobileDetect;
use Packages\Bmcart\App\Template;
use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\ChromePHPHandler;
use Respect\Validation\Validator;
use Packages\Bmcart\Error\CartException;

class Bmcart
{
    const YUPACKET = 10;
    const NEKOPOS = 20;
    const SAGAWA = 1;
    const YAMATO = 30;
    const YUPACK = 40;

    const YUPACKET_FEE = 200;
    const NEKOPOS_FEE = 270;
    const SAGAWA_FEE = 600;
    const YAMATO_FEE = 850;
    const YUPACK_FEE = 1000;

    const SHIP_DISCOUNT_1ST = 5000;
    const SHIP_DISCOUNT_2ND = 10000;

    const DISCOUNT_1ST = 500;
    const DISCOUNT_2ND = 1000;

    const CREDIT = 10;
    const DELIVERY = 200;
    const DELIVERY_FEE = 250;
    const BANK = 100;

    protected $pagent_token_js = null;
    protected $pagent_merchant_id = null;
    protected $pagent_generate_key = null;
    protected $pagent_connect_id = null;
    protected $pagent_connect_password = null;

    public $result_total = 0;
    public $result_totalnum = 0;
    public $result_totalpoint = 0;
    public $result_mail_allowed = true;
    public $result_has_reserve = false;
    public $result_over_discount_1st = false;
    public $result_over_discount_2nd = false;
    public $result_over_discount_3rd = false;
    public $result_yupacket_fee = self::YUPACKET_FEE;
    public $result_nekopos_fee = self::NEKOPOS_FEE;
    public $result_sagawa_fee = self::SAGAWA_FEE;
    public $result_yamato_fee = self::YAMATO_FEE;
    public $result_yupack_fee = self::YUPACK_FEE;

    public $order_completed = false;

    public $morebuy_message = '';
    public $excharge = null;
    public $total_price = null;
    public $usage_of_point = null;
    public $usepoint = null;
    public $max_usable_point = null;
    public $user_get_point = null;
    public $user_updated_point = null;

    public $address_ok = false;
    public $address_postcode1 = null;
    public $address_postcode2 = null;
    public $address_prefecture = null;
    public $address_city = null;
    public $address_town = null;
    public $address_address = null;
    public $address_apartment = null;

    public $shipping_datetime = null;
    public $shipping_choicedate = null;
    public $shipping_choicetime = null;

    public $remark = '';
    public $comment = '';

    public $shipping_method = null;
    public $shipping_timetable = null;

    public $shipping_methods = [
        self::YUPACKET => 'ゆうパケット',
        self::NEKOPOS => 'ネコポス',
        self::SAGAWA => '佐川宅配便',
        self::YAMATO => 'ヤマト宅急便',
        self::YUPACK => 'ゆうパック',
    ];

    public $shipping_method_ids = [
        "yupacket" => self::YUPACKET,
        "nekopos" => self::NEKOPOS,
        "sagawa" => self::SAGAWA,
        "yamato" => self::YAMATO,
        "yupack" => self::YUPACK,
    ];

    private $sagawa_timetable = [
        '0' => array('str_timetable' => '指定なし'),
        '1' => array('str_timetable' => '午前中（8：00～12：00）'),
        '2' => array('str_timetable' => '12：00～14：00'),
        '3' => array('str_timetable' => '14：00～16：00'),
        '4' => array('str_timetable' => '16：00～18：00'),
        '5' => array('str_timetable' => '18：00～21：00'),
    ];

    private $yamato_timetable = [
        '30' => array('str_timetable' => '指定なし'),
        '31' => array('str_timetable' => '午前中（～12：00）'),
        '32' => array('str_timetable' => '14：00～16：00'),
        '33' => array('str_timetable' => '16：00～18：00'),
        '34' => array('str_timetable' => '18：00～20：00'),
        '35' => array('str_timetable' => '19：00～21：00'),
    ];

    private $yupack_timetable = [
        '40' => array('str_timetable' => '指定なし'),
        '41' => array('str_timetable' => '午前中'),
        '42' => array('str_timetable' => '12時頃〜14時頃'),
        '43' => array('str_timetable' => '14時頃〜16時頃'),
        '44' => array('str_timetable' => '16時頃〜18時頃'),
        '45' => array('str_timetable' => '18時頃〜20時頃'),
        '46' => array('str_timetable' => '20時頃〜21時頃'),
    ];

    public $payment_method = null;

    public $payment_methods = [
        self::CREDIT => 'クレジット',
        self::DELIVERY => '代金引換',
        self::BANK => '銀行振込',
    ];

    public $payment_method_ids = [
        "credit" => self::CREDIT,
        "delivery" => self::DELIVERY,
        "bank" => self::BANK,
    ];

    public $instance_data = null;

    public $cart_error = null;
    public $request_cart_error = null;

    public $isMobile = false;

    public $basket = null;
    public $basket_items = null;

    public $template = null;

    public $items_repository = null;
    public $item_entities = null;
    public $user_repository = null;
    public $user_entity = null;

	public $orders_repository = null;
	public $order_details_repository = null;
	public $pre_points_repository = null;
	public $pre_point_details_repository = null;
	public $user_points_repository = null;
	public $user_point_details_repository = null;
	public $stocks_repository = null;

    public $detect = null;

    public $validator = null;

    public $log = null;

    public $db = null;

	public function __construct ($instance_data)
    {
        $this->instance_data = $instance_data;
        $this->template = new Template();

        $this->detect = new MobileDetect;

        $dotenv = Dotenv::create('../../../');
        $dotenv->load();

        $this->log = new Logger('bm-cart');
        $this->log->pushHandler(new StreamHandler('../../../logs/sql.log', Logger::DEBUG));
        $this->log->pushHandler(new ChromePHPHandler(Logger::DEBUG));

        $this->connection();

    }

    public function cartRun ()
    {
		$this->cartInit();
        $this->cartExecute();
        $this->cartVars();
    }

    public function cartInit ()
	{
        $this->init();
	}

	public function cartExecute ()
	{
		$this->execute();
	}

	public function cartVars ()
	{
		$this->vars();
	}

    /*  */
    public function setRequestCartError ()
    {
        $this->request_cart_error = $this->instance_data['request_cart_error'];
    }

    /* login check */
    public function loginCheck ()
    {
        if (!$this->instance_data['login']) {
            throw new CartException('Login check error', 601);
        }
    }

    /* basket check */
    public function basketCheck ()
    {
        if (!$this->instance_data['basket'] && count($this->instance_data['basket']) < 1) {
            throw new CartException('Basket check error', 101);
        }
    }

    /* shipping method check */
    public function shippingCheck()
    {
        if (isset($this->instance_data['shipping_method']) && !!($this->instance_data['shipping_method'])) {
            if (!in_array($this->instance_data['shipping_method'], [(string)self::YUPACKET, (string)self::NEKOPOS, (string)self::SAGAWA, (string)self::YAMATO, (string)self::YUPACK])) {
                throw new CartException('Shipping method check error', 211);
            }
        } else {
            throw new CartException('No shipping method error', 201);
        }
    }

    /* payment method check */
    public function paymentCheck()
    {
        if (isset($this->instance_data['payment_method']) && !!($this->instance_data['payment_method'])) {
            if (!in_array($this->instance_data['payment_method'], [self::CREDIT, self::DELIVERY, self::BANK])) {
                throw new CartException('Payment method check error', 311);
            }
        } else {
            throw new CartException('No payment method error', 301);
        }
    }

    /* point check */
    public function pointCheck()
    {
        if (!is_null($this->instance_data['usage_of_point'])) {
            if ($this->instance_data['usage_of_point'] == 1) {
                if (!$this->instance_data['request_data']['is_ajax']) {
                    if ($this->instance_data['usepoint'] > $this->user_entity->available_point) {
                        throw new CartException('Use point exceeded error', 411);
                    }
                }
            }
        } else {
            throw new CartException('No usage of point error', 401);
        }
    }

    /* Stockover Check */
    public function stockErrorCheck ()
    {
        foreach ($this->basket as $item_id => $basket_item) {
            if ($basket_item['quantity'] > $this->item_entities[$item_id]['stock']) {
                $this->cart_error['stock_error'][$item_id]['basket_quantity'] = $basket_item['quantity'];
                $this->cart_error['stock_error'][$item_id]['stock_quantity'] = $this->item_entities[$item_id]['stock'];
            }

            if ($basket_item['price'] != $this->item_entities[$item_id]['price']) {
                $this->cart_error['stock_error'][$item_id]['basket_price'] = $basket_item['price'];
                $this->cart_error['stock_error'][$item_id]['stock_price'] = $this->item_entities[$item_id]['price'];
            }

            if ($basket_item['point'] != $this->item_entities[$item_id]['point']) {
                $this->cart_error['stock_error'][$item_id]['basket_point'] = $basket_item['point'];
                $this->cart_error['stock_error'][$item_id]['stock_point'] = $this->item_entities[$item_id]['point'];
            }
        }

        if (isset($this->cart_error['stock_error'])) {
            throw new CartException('Stock Error', 511);
        }
    }

    /*  */
    protected function calcShippingFee() {
        foreach ($this->basket as $item_id => $basket_item) {
            $quantity = $basket_item['quantity'];
            if ($quantity < 1) { continue; }

            $item_data = $this->item_entities[$item_id];
            if (!$item_data['mail_allowed']) { $this->result_mail_allowed = false; }
            if ($item_data['is_reserve']) { $this->result_has_reserve = true; }
            $this->result_total+= $item_data['price'] * $quantity;
            $this->result_totalnum+= $quantity;
            $this->result_totalpoint+= $item_data['point'] * $quantity;
        }
        if ($this->result_total >= self::SHIP_DISCOUNT_2ND) {
            $this->result_over_discount_2nd = true;
            $this->result_over_discount_1st = true;
        } else if ($this->result_total >= self::SHIP_DISCOUNT_1ST) {
            $this->result_over_discount_1st = true;
        }

        if ($this->result_over_discount_2nd) {
            $this->result_yupacket_fee = (self::YUPACKET_FEE - self::DISCOUNT_2ND < 0) ? 0 : self::YUPACKET_FEE - self::DISCOUNT_2ND ;
            $this->result_nekopos_fee = (self::NEKOPOS_FEE - self::DISCOUNT_2ND < 0) ? 0 : self::NEKOPOS_FEE - self::DISCOUNT_2ND ;
            $this->result_sagawa_fee = (self::SAGAWA_FEE - self::DISCOUNT_2ND < 0) ? 0 : self::SAGAWA_FEE - self::DISCOUNT_2ND ;
            $this->result_yamato_fee = (self::YAMATO_FEE - self::DISCOUNT_2ND < 0) ? 0 : self::YAMATO_FEE - self::DISCOUNT_2ND ;
            $this->result_yupack_fee = (self::YUPACK_FEE - self::DISCOUNT_2ND < 0) ? 0 : self::YUPACK_FEE - self::DISCOUNT_2ND ;
        } else if ($this->result_over_discount_1st) {
            $this->result_yupacket_fee = (self::YUPACKET_FEE - self::DISCOUNT_1ST < 0) ? 0 : self::YUPACKET_FEE - self::DISCOUNT_1ST ;
            $this->result_nekopos_fee = (self::NEKOPOS_FEE - self::DISCOUNT_1ST < 0) ? 0 : self::NEKOPOS_FEE - self::DISCOUNT_1ST ;
            $this->result_sagawa_fee = (self::SAGAWA_FEE - self::DISCOUNT_1ST < 0) ? 0 : self::SAGAWA_FEE - self::DISCOUNT_1ST ;
            $this->result_yamato_fee = (self::YAMATO_FEE - self::DISCOUNT_1ST < 0) ? 0 : self::YAMATO_FEE - self::DISCOUNT_1ST ;
            $this->result_yupack_fee = (self::YUPACK_FEE - self::DISCOUNT_1ST < 0) ? 0 : self::YUPACK_FEE - self::DISCOUNT_1ST ;
        }

        if ($this->shipping_method) {
            switch ($this->shipping_method) {
                case self::YUPACKET :
                    $this->excharge = $this->result_yupacket_fee;
                    break;
                case self::NEKOPOS :
                    $this->excharge = $this->result_nekopos_fee;
                    break;
                case self::SAGAWA :
                    $this->excharge = $this->result_sagawa_fee;
                    $this->shipping_timetable = $this->sagawa_timetable;
                    break;
                case self::YAMATO :
                    $this->excharge = $this->result_yamato_fee;
                    $this->shipping_timetable = $this->yamato_timetable;
                    break;
                case self::YUPACK :
                    $this->excharge = $this->result_yupack_fee;
                    $this->shipping_timetable = $this->yupack_timetable;
                    break;
            }

            if ($this->payment_method && $this->payment_method == self::DELIVERY) {
                $this->excharge+= self::DELIVERY_FEE;
            }

            if ($this->usepoint) {
                $this->total_price = $this->result_total + $this->excharge - $this->usepoint;
            } else {
                $this->total_price = $this->result_total + $this->excharge;
            }
        }

    }

    protected function setMorebuyMessage() {
        if (!$this->result_over_discount_1st) {
            $this->morebuy_message = 'あと' . (self::SHIP_DISCOUNT_1ST - $this->result_total) . '円で、送料' . self::DISCOUNT_1ST . '円引き<br />';
        } else if (!$this->result_over_discount_2nd) {
            $this->morebuy_message = '現在、送料' . self::DISCOUNT_1ST . '円引き<br />';
            $this->morebuy_message.= 'あと' . (self::SHIP_DISCOUNT_2ND - $this->result_total) . '円で、送料' . self::DISCOUNT_2ND . '円引き<br />';
        } else {
            $this->morebuy_message = '現在、送料' . self::DISCOUNT_2ND . '円引き<br />';
            $this->morebuy_message.= '送料無料！<br />';
        }
        return $this->morebuy_message;
    }

    /* Shipping Method */
    public function setShippingMethod()
    {
        $this->shipping_method = $this->instance_data['shipping_method'];
    }

    /* Payment Method */
    public function setPaymentMethod()
    {
        $this->payment_method = $this->instance_data['payment_method'];
    }

    /* Use Point */
    public function setAvailablePoint()
    {
        $user_id = $this->user_entity->user_id;
        $this->user_entity->available_point = $this->user_points_repository->getUserPoints($user_id);
    }

    public function setUsageOfPoint()
    {
        $this->usage_of_point = $this->instance_data['usage_of_point'];
    }

    public function setUsePoint()
    {
        $this->usepoint = (!is_null($this->instance_data['usepoint'])) ? (int)$this->instance_data['usepoint'] : 0 ;
    }

    public function setMaxUsablePoint()
	{
		$this->max_usable_point = ($this->total_price < $this->user_entity->available_point) ? $this->total_price : $this->user_entity->available_point ;
	}

    public function setCartPoint()
    {
        if ($this->payment_method === self::CREDIT) {
            $this->user_get_point = 0;
        } else {
            $this->user_get_point = $this->result_totalpoint;
        }
        $this->user_updated_point = $this->user_entity->available_point + $this->user_get_point - $this->usepoint;
    }

    /* Shipping Address */
    public function setAddress()
    {
        $address_data = $this->instance_data['address_data'];
        $this->address_ok = $address_data['address_ok'];
        if ($this->address_ok) {
            $this->address_postcode1 = (isset($address_data['postcode1']) && $address_data['postcode1']) ? $address_data['postcode1'] : null ;
            $this->address_postcode2 = (isset($address_data['postcode2']) && $address_data['postcode2']) ? $address_data['postcode2'] : null ;
            $this->address_prefecture = (isset($address_data['prefecture']) && $address_data['prefecture']) ? $address_data['prefecture'] : null ;
            $this->address_city = (isset($address_data['city']) && $address_data['city']) ? $address_data['city'] : null ;
            $this->address_town = (isset($address_data['town']) && $address_data['town']) ? $address_data['town'] : null ;
            $this->address_address = (isset($address_data['address']) && $address_data['address']) ? $address_data['address'] : null ;
            $this->address_apartment = (isset($address_data['apartment']) && $address_data['apartment']) ? $address_data['apartment'] : null ;
        }
    }

    /* Shipping Datetime */
    public function setShippingDatetime()
    {
        $this->shipping_datetime = $this->instance_data['cm'];
        $this->shipping_choicedate = $this->instance_data['choicedate'];
        $this->shipping_choicetime = $this->instance_data['cm2'];
    }

    /* Remark */
    public function setRemark()
    {
        $this->remark = $this->instance_data['remark'];
    }

    /* Comment */
    public function setComment()
    {
        if ($this->address_ok) {
            $postcode = $this->address_postcode1 . '-' . $this->address_postcode2;
            $address = '発送先指定：' . $postcode . " " . $this->address_prefecture . " " . $this->address_city . " " . $this->address_town. " " . $this->address_address . " " . $this->address_apartment;
            $this->comment = $address . "\n" . $this->remark;
        } else {
            $this->comment = $this->remark;
        }
    }

    /* ItemEntities */
    public function setItemEntities ()
    {
        $this->basket = $this->instance_data['basket'];
        $item_array = array_keys($this->basket);
        $this->item_entities = $this->items_repository->findById($item_array)->keyBy('item_id');
    }

    /* UserEntity */
    public function setUserEntity ()
    {
        $username = $this->instance_data['login'];
        $this->user_entity = $this->getUserEntity($username);
        if ($this->user_entity) {
            $this->user_entity->is_admin = $this->isAdmin();
        }
    }

    public function getUserEntity ($username)
    {
        return $this->user_repository->findByUsername($username);
    }

    public function isLoggedIn ()
    {
        return ($this->user_entity) ? true : false ;
    }

    public function isAdmin ()
    {
        return ($this->user_entity->group_id === 3) ? true : false ;
    }

    /* Pagent */
    public function setPagentKeys ()
    {
        $this->pagent_token_js = getenv('PAYGENT_TOKEN_JS');
        $this->pagent_merchant_id = getenv('PAYGENT_MERCHANT_ID');
        $this->pagent_generate_key = getenv('PAYGENT_GENERATE_KEY');
        $this->pagent_connect_id = getenv('PAYGENT_CONNECT_ID');
        $this->pagent_connect_password = getenv('PAYGENT_CONNECT_PASSWORD');
    }

    /* Database */
    public function connection ()
    {

        $dbname = getenv('DBNAME');
        $dbuser = getenv('DBUSER');
        $dbpass = getenv('DBPASS');

        $this->db = new Capsule;
        $this->db->addConnection([
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => $dbname,
            'username'  => $dbuser,
            'password'  => $dbpass,
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);

        $this->db->setAsGlobal();
        $this->db->bootEloquent();
    }

}
