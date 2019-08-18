<?php

namespace Packages\Bmcart\Model;

use Illuminate\Database\Eloquent\Model;

class PrePointsEloquent extends Model
{
    protected $table = 'pre_points';

    const UPDATED_AT = 'modified_at';

    protected $guarded = ['id'];
}