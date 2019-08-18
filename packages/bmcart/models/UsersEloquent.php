<?php

namespace Packages\Bmcart\Model;

use Illuminate\Database\Eloquent\Model;
use Packages\Bmcart\Model\UsersNew;

class Users extends Model
{
    protected $table = 'users';

    protected $hidden = ['password'];

}