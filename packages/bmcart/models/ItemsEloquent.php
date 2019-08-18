<?php

namespace Packages\Bmcart\Model;

use Illuminate\Database\Eloquent\Model;
use Packages\Bmcart\Model\ItemStocksEloquent;

class ItemsEloquent extends Model
{
    protected $table = 'items';

    protected $guarded = ['id'];

}