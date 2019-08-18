<?php

namespace Packages\Bmcart\Model;

interface OrdersRepositoryInterface
{

    public function getOrderById($order_id);

    public function insertOrder ($insert_data);

}