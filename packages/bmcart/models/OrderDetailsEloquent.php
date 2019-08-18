<?php

namespace Packages\Bmcart\Model;

use Illuminate\Database\Eloquent\Model;

class OrderDetailsEloquent extends Model
{
    protected $table = 'order_details';

    const UPDATED_AT = 'modified_at';

    protected $guarded = ['ID'];
}