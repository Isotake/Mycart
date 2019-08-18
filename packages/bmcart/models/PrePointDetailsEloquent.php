<?php

namespace Packages\Bmcart\Model;

use Illuminate\Database\Eloquent\Model;

class PrePointDetailsEloquent extends Model
{
    protected $table = 'pre_point_details';

    const UPDATED_AT = 'modified_at';

    protected $guarded = ['id'];
}