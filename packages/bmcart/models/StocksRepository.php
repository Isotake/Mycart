<?php

namespace Packages\Bmcart\Model;

use Packages\Bmcart\Model\StocksEloquent;
use Packages\Bmcart\Model\StocksRepositoryInterface;

class StocksRepository implements StocksRepositoryInterface
{

    protected $model = null;

    public function __construct()
    {
        $this->model = new StocksEloquent();
    }

    public function findById($item_id)
    {
        return $this->model->whereIn('item_id', $item_id)->first();
    }

    public function updateStocks ($update_data)
    {
        foreach ($update_data as $item_id => $quantity) {
            $this->model->where('item_id', $item_id)->decrement('stock', (int)$quantity);
        }
        return true;
    }

    public function updateReserveStocks ($update_data)
    {
        foreach ($update_data as $item_id => $quantity) {
            $this->model->where('item_id', $item_id)->decrement('reserve_stock', (int)$quantity);
        }
        return true;
    }

}