<?php

namespace Packages\Bmcart\Model;

use Illuminate\Database\Eloquent\Model;

class UserPointsEloquent extends Model
{
    protected $table = 'user_points';

    const UPDATED_AT = 'modified_at';

    protected $guarded = ['id'];
}