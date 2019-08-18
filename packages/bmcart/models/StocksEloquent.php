<?php

namespace Packages\Bmcart\Model;

use Illuminate\Database\Eloquent\Model;

class StocksEloquent extends Model
{
    protected $table = 'stocks';

    public $timestamps = false;

    protected $guarded = ['id'];
}