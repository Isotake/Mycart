<?php

namespace Packages\Bmcart\App;

use Packages\Bmcart\Model\ItemsRepository;
use Packages\Bmcart\Model\UsersRepository;
use Packages\Bmcart\Model\UserPointsRepository;

class BmcartStep3 extends Bmcart
{
	public function __construct($instance_data)
	{
		parent::__construct($instance_data);

		$this->items_repository = new ItemsRepository();
		$this->user_repository = new UsersRepository();
		$this->user_points_repository = new UserPointsRepository();

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

        $this->stockErrorCheck();

    }

    public function execute ()
    {

        $this->calcShippingFee();
		$this->setMaxUsablePoint();
        if ($this->usepoint) {
            $this->setCartPoint();
        }

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
            "remark" => $this->remark,

            "pagent_merchant_id" => $this->pagent_merchant_id,
            "pagent_generate_key" => $this->pagent_generate_key,


            'title' => 'STEP3 | Bigwebカート',
            'load_js' => [
                '//ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/jquery.validate.min.js',
                $this->pagent_token_js,
            ],
            'custom_js' => 'custom-step3',
            'step_number' => 3,
            'body_class' => ($this->isMobile) ? 'mobile step3' : 'pc step3' ,
        ];

        $htmlescape_whitelist = [
            'morebuy_message',
            'debug_message',
            'remark',
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
//            "remark" => $this->remark,
//
//            "pagent_merchant_id" => $this->pagent_merchant_id,
//            "pagent_generate_key" => $this->pagent_generate_key,
//        ]);
//
//        $this->template->setVars([
//            'title' => 'STEP3 | Bigwebカート',
//            'load_js' => [
//                '//ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/jquery.validate.min.js',
//                $this->pagent_token_js,
//            ],
//            'custom_js' => 'custom-step3',
//            'step_number' => 3,
//            'body_class' => ($this->isMobile) ? 'mobile step3' : 'pc step3' ,
//        ]);

    }

}
