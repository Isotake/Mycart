<?php

namespace Packages\Bmcart\Model;

interface PrePointsRepositoryInterface
{
    /**
     * @param $usename
     * @return mixed
     */
    public function getPrePoints($user_id);

    public function insertPrePoints ($order_id, $user_id, $operator_id, $point_get, $use_point);
}