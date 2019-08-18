<?php

namespace Packages\Bmcart\Model;

interface StocksRepositoryInterface
{

    /**
     * @param $item_id
     * @return mixed
     */
    public function findById($item_id);

    public function updateStocks ($update_data);

    public function updateReserveStocks ($update_data);

}