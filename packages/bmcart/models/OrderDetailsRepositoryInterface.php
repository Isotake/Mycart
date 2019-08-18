<?php

namespace Packages\Bmcart\Model;

interface OrderDetailsRepositoryInterface
{

    public function getOrderDetailsById ($order_id);

    public function insertOrderDetails ($insert_data);

}