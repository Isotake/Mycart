<?php

namespace Packages\Bmcart\Model;

use Packages\Bmcart\Model\ItemsEloquent;
use Packages\Bmcart\Model\ItemsRepositoryInterface;

class ItemsRepository implements ItemsRepositoryInterface
{

    protected $model = null;

    public function __construct()
    {
        $this->model = new ItemsEloquent();
    }

    public function findById($item_array)
    {
        return $this->model
            ->join('stocks', 'items.id', '=', 'stocks.item_id')
            ->whereIn('items.id', $item_array)
            ->select(
                'items.id as item_id',
                'items.name',
                'items.maker',
                'items.prefecture',
                'items.comment',
                'items.filename',
                'items.is_reserve',
                'items.mail_allowed',
                'stocks.stock',
                'stocks.reserve_stock',
                'stocks.price',
                'stocks.point'
            )
            ->get();
    }

    public function findByIdForSharedLock ($item_array)
    {
        return $this->model
            ->join('stocks', 'items.id', '=', 'stocks.item_id')
            ->whereIn('items.id', $item_array)
            ->select(
                'items.id as item_id',
                'stocks.stock',
                'stocks.reserve_stock',
                'stocks.price',
                'stocks.point'
            )
            ->sharedLock()
            ->get();
    }

    public function findByIdForExclusiveLock ($item_array)
    {
        return $this->model
            ->join('stocks', 'items.id', '=', 'stocks.item_id')
            ->whereIn('items.id', $item_array)
            ->select(
                'items.id as item_id',
                'stocks.stock',
                'stocks.reserve_stock',
                'stocks.price',
                'stocks.point'
            )
            ->lockForUpdate()
            ->get();
    }

}