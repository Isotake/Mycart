<?php

namespace Packages\Bmcart\Model;

use Packages\Bmcart\Model\Users;
use Packages\Bmcart\Model\UsersRepositoryInterface;

class UsersRepository implements UsersRepositoryInterface
{

    protected $model = null;

    public function __construct()
    {
        $this->model = new Users();
    }

    public function findByUsername($username)
    {
        return $this->model->join('user_details', 'users.id', '=', 'user_details.user_id')
            ->where('users.username', $username)
            ->select(
                'users.id as user_id',
                'users.username',
                'users.email',
                'users.group_id',
                'user_details.first_name',
                'user_details.first_name_mb',
                'user_details.last_name',
                'user_details.last_name_mb',
                'user_details.phone',
                'user_details.postcode',
                'user_details.prefecture',
                'user_details.city',
                'user_details.town',
                'user_details.address',
                'user_details.apartment'
            )
            ->first();
    }

    public function findById($users_id)
    {
        return $this->model->join('user_details', 'users.id', '=', 'user_details.user_id')
            ->where('users.id', $users_id)
            ->select(
                'users.id as user_id',
                'users.username',
                'users.email',
                'users.group_id',
                'user_details.first_name',
                'user_details.first_name_mb',
                'user_details.last_name',
                'user_details.last_name_mb',
                'user_details.phone',
                'user_details.postcode',
                'user_details.prefecture',
                'user_details.city',
                'user_details.town',
                'user_details.address',
                'user_details.apartment'
            )
            ->first();
    }

    public function getLegacyUserId ($user_id)
    {
        return $this->model->where('id', $user_id)->value('users_new_id');
    }
}