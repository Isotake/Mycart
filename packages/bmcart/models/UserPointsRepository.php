<?php

namespace Packages\Bmcart\Model;

use Packages\Bmcart\Model\UserPointsEloquent;
use Packages\Bmcart\Model\UserPointsRepositoryInterface;
use Packages\Bmcart\Model\UsersRepository;

class UserPointsRepository implements UserPointsRepositoryInterface
{

    protected $model = null;

    public function __construct()
    {
        $this->model = new UserPointsEloquent();
    }

    public function getUserPoints($user_id)
    {
        $entity = $this->model->where('user_id', $user_id)->first();
        if ($entity) {
            return $entity->point;
        } else {
            return 0;
        }
    }

    public function updateUserPoint($user_id, $point, $updateOrCreate = true)
    {
        if ($updateOrCreate) {
            $entity = $this->model->updateOrCreate(
                ['user_id' => $user_id],
                ['point' => $point]
            );
            return $entity;
        } else {
            $entity = $this->getUserPoints($user_id);
            if ($entity) {
                $entity->point = $point;
                $entity->save();
                return $entity;
            } else {
                return false;
            }
        }
    }

}