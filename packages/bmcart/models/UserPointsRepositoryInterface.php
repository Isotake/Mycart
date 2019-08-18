<?php

namespace Packages\Bmcart\Model;

interface UserPointsRepositoryInterface
{
    /**
     * @param $usename
     * @return mixed
     */
    public function getUserPoints($user_id);

    /**
     * @param $user_id
     * @param $point
     * @param bool $updateOrCreate
     * @return mixed
     */
    public function updateUserPoint($user_id, $point, $updateOrCreate = true);

}