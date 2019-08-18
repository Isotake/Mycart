<?php

namespace Packages\Bmcart\Model;

use Packages\Bmcart\Model\OrdersEloquent;
use Packages\Bmcart\Model\OrdersRepositoryInterface;

class OrdersRepository implements OrdersRepositoryInterface
{

    protected $model = null;

    public function __construct()
    {
        $this->model = new OrdersEloquent();
    }

    public function getOrderById($order_id)
    {
        return $this->model
            ->where('ID', $order_id)
            ->get();
    }

    public function insertOrder ($insert_data)
    {
        $entity = $this->model;
        $timestamp = date("Y-m-d H:i:s", time());

        $entity->state = $insert_data['state'];
        $entity->shipping_fee = $insert_data['shipping_fee'];
        $entity->shipping = $insert_data['shipping'];
        $entity->arriving_date = $insert_data['arriving_date'];
        $entity->arriving_time = $insert_data['arriving_time'];
        $entity->payment = $insert_data['payment'];
        $entity->payment_result = $insert_data['payment_result'];
        $entity->user_id = $insert_data['user_id'];
        $entity->has_reserve = $insert_data['has_reserve'];
        $entity->comment = $insert_data['comment'];
        $entity->shop_comment = $insert_data['shop_comment'];
        $entity->created_at = $timestamp;
        $entity->modified_at = $timestamp;

        $entity->save();

        return $entity;
    }

}