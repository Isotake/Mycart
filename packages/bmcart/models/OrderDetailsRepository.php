<?php

namespace Packages\Bmcart\Model;

use Packages\Bmcart\Model\OrderDetailsEloquent;
use Packages\Bmcart\Model\OrderDetailsRepositoryInterface;

class OrderDetailsRepository implements OrderDetailsRepositoryInterface
{

    protected $model = null;

    public function __construct()
    {
        $this->model = new OrderDetailsEloquent();
    }

    public function getOrderDetailsById($order_id)
    {
        return $this->model
            ->where('order_ID', $order_id)
            ->get();
    }

    public function insertOrderDetails ($order_details)
    {

        $entity = $this->model;
        $timestamp = date("Y-m-d H:i:s", time());

        $insert_data = [];
        foreach ($order_details as $order_detail) {
            $insert_data[] = [
                'order_id' => $order_detail['order_id'],
                'item_id' => $order_detail['item_id'],
                'user_id' => $order_detail['user_id'],
                'price' => $order_detail['price'],
                'number' => $order_detail['number'],
                'point' => $order_detail['point'],
                'created_at' => $timestamp,
                'modified_at' => $timestamp,
            ];
        }

        $entity->insert($insert_data);

        return $entity;

    }

}