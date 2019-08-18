<?php

namespace Packages\Bmcart\Model;

use Illuminate\Database\Eloquent\Model;

class UserPointLogsEloquent extends Model
{
    protected $table = 'user_point_logs';

    const UPDATED_AT = 'modified_at';

    protected $guarded = ['id'];
}