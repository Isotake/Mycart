<?php

namespace Packages\Bmcart\Model;

use Illuminate\Database\Eloquent\Model;

class OrdersEloquent extends Model
{
    protected $table = 'orders';

    const UPDATED_AT = 'modified_at';

    protected $guarded = ['ID'];
}